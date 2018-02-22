<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResponsableCfa
 *
 * @ORM\Table(name="responsable_cfa", indexes={@ORM\Index(name="IDX_694E8C9A81B56FBD", columns={"id_responsable_iut"})})
 * @ORM\Entity(repositoryClass="App\Repository\ResponsableCfaRepository")
 */
class ResponsableCfa
{
    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_responsable_iut", referencedColumnName="id")
     * })
     */
    private $responsableIut;

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
     * @return User
     */
    public function getResponsableIut(): User
    {
        return $this->responsableIut;
    }

    /**
     * @param User $responsableIut
     */
    public function setResponsableIut(User $responsableIut): void
    {
        $this->responsableIut = $responsableIut;
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

