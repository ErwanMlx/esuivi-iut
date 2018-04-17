<?php

namespace App\Repository;

use App\Entity\Apprenti;
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
}
