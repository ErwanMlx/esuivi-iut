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

//        ->andWhere($qb->expr()->orX(
//        $qb->expr()->eq('ed.dateDebut',
//            ($qb->addSelect('ed2')
//
//                ->from('EtapeDossier', 'ed2')
//                ->where('ed2.typeEtape=te.id')
//                ->andWhere('ed2.dossier=:dossier'))),
//        $qb->expr()->isNull('ed.dateDebut')))

        return $qb->execute();


//        $conn = $this->getEntityManager()->getConnection();
//
//        $sql = '
//       SELECT * from type_etape te
//            LEFT JOIN etape_dossier ed ON (ed.id_type_etape = te.id)
//            WHERE
//            (id_dossier=:dossier OR id_dossier IS NULL)
//            AND
//            (ed.date_debut = (SELECT MAX(ed2.date_debut) FROM etape_dossier ed2 WHERE ed2.id_type_etape=te.id AND ed2.id_dossier=:dossier) OR ed.date_debut IS NULL)
//            ORDER BY te.position_etape
//        ';
//        $stmt = $conn->prepare($sql);
//        $stmt->execute(['dossier' => $id_dossier]);
//
//        // returns an array of arrays (i.e. a raw data set)
//        return $stmt->fetchAll();
    }
    public function tempsMoyenDossier(){

		$conect = $this->getEntityManager()->getConnection();

		$sql = "
		select avg(extract(epoch from (z.date_fin_doss - z.date_debut_doss))) as tempsMoyen
		from (
			select max(a.date_validation) as date_fin_doss, min(a.date_debut) as date_debut_doss
			from etape_dossier a join dossier_apprenti b on (b.id = a.id_dossier)
			where b.etat <> 'abandonne'
			group by a.id_dossier
		) as z
		";

		$statement = $conect->prepare($sql);
		$statement->execute();

		return $statement->fetchAll();

	/* Requête propre */	
	/*	$qb2 = $this->getEntityManager()->createQueryBuilder(); 
		$qb = $this->getEntityManager()->createQueryBuilder()
			->select($qb2->expr()->avg('c.date_fin_doss - c.date_debut_doss'))
			->from($qb2
				->select(
					array(
						$qb2->expr()->max('a.date_validation').' as date_fin_doss', 
						$qb2->expr()->min('a.date_debut').' as date_debut_doss'
					)
				)
				->from(EtapeDossier::class, 'a')
				->join(DossierApprenti::class, 'b','WITH','a.id_dossier = b.id')
				->where('b.etat <> :etat')
				->groupBy('a.id_dossier')
				,
				'c'
			)
			->setParameter(':etat','abandonné')
			->getQuery();*/

		/*return $qb->execute();*/
	}
}
