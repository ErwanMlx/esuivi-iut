<?php

namespace App\Repository;

use App\Entity\TypeEtape;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TypeEtapeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TypeEtape::class);
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('t')
            ->where('t.something = :value')->setParameter('value', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function findAll()
    {
        return $this->findBy(array(), array('id' => 'ASC'));
    }

    //
    public function findAllGreaterThanID($id): array
    {
        $qb = $this->createQueryBuilder('te')
            ->andWhere('te.ID > :id')
            ->setParameter('id', $id)
            ->orderBy('te.ID', 'ASC')
            ->getQuery();

        return $qb->execute();
    }

    public function  getNbTypeEtape() {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->getQuery()
            ->getSingleScalarResult();
    }


}
