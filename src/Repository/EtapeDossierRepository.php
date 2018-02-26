<?php

namespace App\Repository;

use App\Entity\EtapeDossier;
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
}
