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

    //SELECT * FROM utilisateur WHERE id IN (SELECT id_compte from apprenti WHERE id_dossier_apprenti IN
    //
    //(SELECT da from dossier_apprenti da LEFT JOIN etape_dossier ed ON (ed.id = da.id_etape_actuelle)

    //WHERE da.etat='En cours'
    //
    //AND ed.date_debut<(NOW()-'15 DAYS'::INTERVAL)
    //
    //AND ed.id_type_etape IN (SELECT id from type_etape WHERE type_validateur = 'ROLE_APPRENTI')));
    public function searchApprentiRetard($nbJours) {
        $em = $this->getEntityManager();
        $liste_type_etape = $em->createQueryBuilder()
            ->select('te')
            ->from(TypeEtape::class, 'te')
            ->where('te.typeValidateur = \'ROLE_APPRENTI\'');

        $liste_dossier = $em->createQueryBuilder();
        $liste_dossier->select('da')
            ->from(DossierApprenti::class, 'da')
            ->innerJoin(EtapeDossier::class, 'ed', 'WITH', 'ed = da.etapeActuelle')
            ->where('da.etat = \'En cours\'')
            ->andWhere('ed.dateDebut<(DATE_SUB(CURRENT_TIMESTAMP(), :nbJours, \'DAY\'))')
            ->andWhere($liste_dossier->expr()->in('ed.typeEtape', $liste_type_etape->getDQL()))
            ->setParameter('nbJours', $nbJours);

        $liste_apprenti = $this->createQueryBuilder('a');
        $liste_apprenti->where($liste_apprenti->expr()->in('a.dossier', $liste_dossier->getDQL()))
            ->setParameter('nbJours', $nbJours);
        return $liste_apprenti->getQuery()->execute();
    }
}
