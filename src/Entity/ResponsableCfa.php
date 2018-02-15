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
     * @var Compte
     *
     * @ORM\ManyToOne(targetEntity="Compte")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_responsable_iut", referencedColumnName="id")
     * })
     */
    private $responsableIut;

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
     * @return Compte
     */
    public function getResponsableIut(): Compte
    {
        return $this->responsableIut;
    }

    /**
     * @param Compte $responsableIut
     */
    public function setResponsableIut(Compte $responsableIut): void
    {
        $this->responsableIut = $responsableIut;
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

