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
     * @var Compte
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Compte")
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
     * @return Compte
     */
    public function getCompte(): Compte
    {
        return $this->compte;
    }

    /**
     * @param Compte $compte
     */
    public function setCompte(Compte $compte): void
    {
        $this->compte = $compte;
    }

}

