<?php
namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;


class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /*
     * Pour chercher un utilisateur (n'importe lequel) ayant un certain rôle
     */
    public function findOneByRole($role) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
            ->from($this->_entityName, 'u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"' . $role . '"%')
            ->setMaxResults(1);

        return $qb->getQuery()->getSingleResult();
    }

    public function findByRole($role) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
            ->from($this->_entityName, 'u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"' . $role . '"%')
            ->orderBy('u.nom', 'ASC')
            ->addOrderBy('u.prenom', 'ASC');
        return $qb->getQuery()->execute();
    }

    public function findAllIUT() {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
            ->from($this->_entityName, 'u')
            ->where('u.roles LIKE :role_iut')
            ->orWhere('u.roles LIKE :role_admin')
            ->setParameter('role_iut', '%"' . "ROLE_IUT" . '"%')
            ->setParameter('role_admin', '%"' . "ROLE_ADMIN" . '"%')
            ->orderBy('u.nom', 'ASC')
            ->addOrderBy('u.prenom', 'ASC');

        return $qb->getQuery()->execute();
    }
}