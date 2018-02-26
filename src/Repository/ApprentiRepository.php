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
            ->where('c.nom LIKE :search')
            ->orWhere('c.prenom LIKE :search')
            ->orWhere('CONCAT(c.nom, \' \', c.prenom) LIKE :search')
            ->orWhere('CONCAT(c.prenom, \' \', c.nom) LIKE :search')
            ->setParameter('search', '%'.$search.'%')
            ->orderBy('c.nom', 'ASC')
            ->getQuery();

        return $qb->execute();
    }
}
