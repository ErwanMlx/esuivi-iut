<?php

namespace App\Repository;

use App\Entity\Apprenti;
use App\Entity\DossierApprenti;
use App\Entity\EtapeDossier;
use App\Entity\TypeEtape;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ApprentiRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Apprenti::class);
    }

//select * from apprenti join utilisateur ON (id_compte=id) WHERE (nom||' '||prenom) LIKE '%PM Erwan%' OR (prenom||' '||nom) LIKE '%PM Erwan%' OR nom LIKE '%PM Erwan%' OR prenom LIKE '%PM Erwan%'
    public function search($search) {
        $qb = $this->createQueryBuilder('a')
            ->join('a.compte', 'c')
            ->where('LOWER(c.nom) LIKE LOWER(:search)')
            ->orWhere('LOWER(c.prenom) LIKE LOWER(:search)')
            ->orWhere('LOWER(CONCAT(c.nom, \' \', c.prenom)) LIKE LOWER(:search)')
            ->orWhere('LOWER(CONCAT(c.prenom, \' \', c.nom)) LIKE LOWER(:search)')
            ->setParameter('search', '%'.$search.'%')
            ->orderBy('c.nom', 'ASC')
            ->addOrderBy('c.prenom', 'ASC')
            ->getQuery();

        return $qb->execute();
    }

    public function searchForMaitreApp($search, $id_maitre_app) {
        $qb = $this->createQueryBuilder('a')
            ->join('a.dossier', 'd')
            ->where('d.maitreApprentissage = :id_maitre_app')
            ->andWhere('LOWER(c.prenom) LIKE LOWER(:search) 
            OR LOWER(CONCAT(c.nom, \' \', c.prenom)) LIKE LOWER(:search)
            OR LOWER(CONCAT(c.prenom, \' \', c.nom)) LIKE LOWER(:search)')
            ->setParameter('id_maitre_app', $id_maitre_app)
            ->setParameter('search', '%'.$search.'%')
            ->join('a.compte', 'c')
            ->orderBy('c.nom', 'ASC')
            ->addOrderBy('c.prenom', 'ASC')
            ->getQuery();

        return $qb->execute();
    }

    public function findByMaitreApp($id_maitre_app) {
        $qb = $this->createQueryBuilder('a')
            ->join('a.dossier', 'd')
            ->where('d.maitreApprentissage = :id_maitre_app')
            ->setParameter('id_maitre_app', $id_maitre_app)
            ->join('a.compte', 'c')
            ->orderBy('c.nom', 'ASC')
            ->addOrderBy('c.prenom', 'ASC')
            ->getQuery();

        return $qb->execute();
    }

//    SELECT * FROM UTILISATEUR WHERE ID IN (SELECT ID_COMPTE FROM APPRENTI WHERE ID_DOSSIER_APPRENTI IN
//
//    (SELECT DA FROM DOSSIER_APPRENTI DA LEFT JOIN ETAPE_DOSSIER ED ON (ED.ID = DA.ID_ETAPE_ACTUELLE)
//
//    WHERE DA.ETAT='EN COURS'
//
//    AND ED.DATE_DEBUT=(NOW()-'15 DAYS'::INTERVAL)
//
//    AND ED.ID_TYPE_ETAPE IN (SELECT ID FROM TYPE_ETAPE WHERE TYPE_VALIDATEUR = 'ROLE_APPRENTI')));
    public function searchApprentiRetard($nbJours, $typeValidateur) {
        $em = $this->getEntityManager();
        $liste_type_etape = $em->createQueryBuilder()
            ->select('te')
            ->from(TypeEtape::class, 'te')
            ->where('te.typeValidateur = :typeValidateur');

        $liste_dossier = $em->createQueryBuilder();
        $liste_dossier->select('da')
            ->from(DossierApprenti::class, 'da')
            ->innerJoin(EtapeDossier::class, 'ed', 'WITH', 'ed = da.etapeActuelle')
            ->where('da.etat = \'En cours\'')
            ->andWhere('DATE_DIFF(CURRENT_TIMESTAMP(), ed.dateDebut) = :nbJours')
            ->andWhere($liste_dossier->expr()->in('ed.typeEtape', $liste_type_etape->getDQL()));

        $liste_apprenti = $this->createQueryBuilder('a');
        $liste_apprenti->where($liste_apprenti->expr()->in('a.dossier', $liste_dossier->getDQL()))
            ->setParameter('nbJours', $nbJours)
            ->setParameter('typeValidateur', $typeValidateur);

        return $liste_apprenti->getQuery()->execute();
    }
}
