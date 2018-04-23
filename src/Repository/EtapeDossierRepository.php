<?php

namespace App\Repository;

use App\Entity\EtapeDossier;
use App\Entity\DossierApprenti;
use App\Entity\TypeEtape;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Query\ResultSetMapping;

class EtapeDossierRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EtapeDossier::class);
    }

    //select * from type_etape te
    //LEFT JOIN etape_dossier ed ON (ed.id_type_etape = te.id)
    //WHERE
    //(id_dossier=2 OR id_dossier IS NULL)
    //AND
    //(ed.date_debut = (SELECT MAX(ed2.date_debut) FROM etape_dossier ed2 WHERE ed2.id_type_etape=te.id AND ed2.id_dossier=2) OR ed.date_debut IS NULL)
    //ORDER BY te.position_etape
    public function findAllCurrent($id_dossier)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb = $em->createQueryBuilder()
            ->select('te')
            ->from(TypeEtape::class, 'te')
            ->leftJoin(EtapeDossier::class, 'ed', 'WITH', 'ed.typeEtape = te.id')
            ->where($qb->expr()->orX(
                $qb->expr()->eq('ed.dossier', ':dossier'),
                $qb->expr()->isNull('ed.dossier')))
            ->andWhere($qb->expr()->orX(
                $qb->expr()->eq('ed.dateDebut',
                    ('(SELECT MAX(ed2.dateDebut) FROM '.EtapeDossier::class.' ed2 WHERE ed2.typeEtape=te.id AND ed2.dossier=:dossier)')),
                $qb->expr()->isNull('ed.dateDebut')))
            ->setParameter(':dossier', $id_dossier)
            ->orderBy('te.positionEtape', 'ASC')
            ->getQuery();

        return $qb->execute();
    }

	public function tempsMoyenDossier() {
        $em = $this->getEntityManager();

        $dates_dossier = $em->createQueryBuilder()
            ->select('DATE_DIFF(MAX(ed.dateValidation), MIN(ed.dateDebut)) AS date_diff')
            ->from(EtapeDossier::class, 'ed')
            ->join(DossierApprenti::class, 'd', 'WITH', 'd.id = ed.dossier')
            ->where('d.etat = \'Terminé\'')
            ->groupBy('ed.dossier');

        $tab_dates_dossier = $dates_dossier->getQuery()->execute();
        $tab_dates_dossier = array_column($tab_dates_dossier, "date_diff");
        if(count($tab_dates_dossier) != 0) {
            $tempsMoyenDossiers = array_sum($tab_dates_dossier) / count($tab_dates_dossier);
        }
        else {
            $tempsMoyenDossiers = null;
        }

        return $tempsMoyenDossiers;

    }

    public function nombreAbandonsDossier(){
        /* select count(*)
        from dossier_apprenti
        where etat = 'Abandonné'; */

		$qb = $this->getEntityManager()->createQueryBuilder()
			->select('count(doss) as nbAbandons')
			->from(DossierApprenti::class, 'doss')
			->where('doss.etat = :state')
			->setParameter(':state','Abandonné');

		return $qb->getQuery()->execute()[0]["nbAbandons"];
    }

    public function tempsMoyenEtapes(){
        /* select avg(a.date_validation - a.date_debut) as tempsMoyenEtape
        from etape_dossier a join type_etape b on a.id_type_etape = b.id
        where a.date_validation is not null
        group by b.id
        order by b.position_etape; */

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('avg(DATE_DIFF(etapes.dateValidation, etapes.dateDebut)) as tempsMoyenEtape')
            ->from(EtapeDossier::class, 'etapes')
            ->join(TypeEtape::class, 'doss', 'with', 'etapes.typeEtape = doss.id')
            ->where('etapes.dateValidation is not null')
            ->groupby('doss.id')
            ->orderby('doss.positionEtape', 'asc');

        $tempsMoyenEtapes = array_column($qb->getQuery()->execute(), "tempsMoyenEtape");

        $tempsMoyenEtapes = array_map('intval', $tempsMoyenEtapes);
        $tempsMoyenEtapes = array_map('ceil', $tempsMoyenEtapes);
        return $tempsMoyenEtapes;
    }

    //(select te.position_etape, count(*) from type_etape te join etape_dossier ed on (ed.id_type_etape = te.id)
    //join dossier_apprenti de on (ed.id = de.id_etape_actuelle)
    //where de.etat='Abandonné'
    //GROUP BY te.position_etape
    //ORDER BY te.position_etape)
    //
    //UNION
    //
    //(SELECT position_etape, 0 from type_etape WHERE id NOT IN
    //(select DISTINCT ed.id_type_etape from etape_dossier ed
    //join dossier_apprenti de on (ed.id = de.id_etape_actuelle)
    //where de.etat='Abandonné'))
    //ORDER BY position_etape;
    public function tauxAbandonEtapes() {
        $qb1 = $this->getEntityManager()->createQueryBuilder()
            ->select('te.positionEtape, count(te) AS taux')
            ->from(TypeEtape::class, 'te')
            ->join(EtapeDossier::class, 'ed', 'WITH', 'ed.typeEtape = te.id')
            ->join(DossierApprenti::class, 'da', 'WITH', 'ed.id = da.etapeActuelle')
            ->where('da.etat = \'Abandonné\'')
            ->groupby('te.positionEtape')
            ->orderby('te.positionEtape', 'ASC');

        $tab1 = $qb1->getQuery()->execute();

        $qb2 = $this->getEntityManager()->createQueryBuilder()
            ->select('IDENTITY(ed.typeEtape)')
            ->from(EtapeDossier::class, 'ed')
            ->join(DossierApprenti::class, 'da', 'WITH', 'ed.id = da.etapeActuelle')
            ->where('da.etat = \'Abandonné\'');

        $qb3 = $this->getEntityManager()->createQueryBuilder();
        $qb3->select('te.positionEtape, 0 AS taux')
            ->from(TypeEtape::class, 'te')
            ->where($qb3->expr()->notIn('te', $qb2->getDQL()));

        $tab2 = $qb3->getQuery()->execute();

        $tab3 = array_merge($tab1, $tab2);

        asort($tab3);

        $tab3 = array_column($tab3, "taux");

        return $tab3;
    }
}
