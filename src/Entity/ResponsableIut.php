<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResponsableIut
 *
 * @ORM\Table(name="responsable_iut")
 * @ORM\Entity(repositoryClass="App\Repository\ResponsableIutRepository")
 */
class ResponsableIut
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="acces", type="boolean", nullable=false)
     */
    private $acces = false;

    /**
     * @var User
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_compte", referencedColumnName="id")
     * })
     */
    private $compte;

    /**
     * @return bool
     */
    public function isAcces(): bool
    {
        return $this->acces;
    }

    /**
     * @param bool $acces
     */
    public function setAcces(bool $acces): void
    {
        $this->acces = $acces;
    }

    /**
     * @return User
     */
    public function getCompte(): User
    {
        return $this->compte;
    }

    /**
     * @param User $compte
     */
    public function setCompte(User $compte): void
    {
        $this->compte = $compte;
    }

}

