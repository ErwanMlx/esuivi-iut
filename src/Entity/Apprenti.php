<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Apprenti
 *
 * @ORM\Table(name="apprenti", indexes={@ORM\Index(name="IDX_2CB7951C81B56FBD", columns={"id_responsable_iut"}), @ORM\Index(name="IDX_2CB7951CE77D22BA", columns={"id_dossier_apprenti"})})
 * @ORM\Entity(repositoryClass="App\Repository\ApprentiRepository")
 */
class Apprenti extends Compte
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="apprenti_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=10, nullable=true)
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=256, nullable=true)
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="code_postal", type="string", length=5, nullable=true)
     */
    private $codePostal;

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=64, nullable=true)
     */
    private $ville;

    /**
     * @var ResponsableIut
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ResponsableIut")
     * @ORM\JoinColumn(name="id_responsable_iut", referencedColumnName="id")
     */
    private $ResponsableIut;

    /**
     * @var DossierApprenti
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\DossierApprenti")
     * @ORM\JoinColumn(name="id_dossier_apprenti", referencedColumnName="id")
     */
    private $DossierApprenti;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTelephone(): string
    {
        return $this->telephone;
    }

    /**
     * @param string $telephone
     */
    public function setTelephone(string $telephone): void
    {
        $this->telephone = $telephone;
    }

    /**
     * @return string
     */
    public function getAdresse(): string
    {
        return $this->adresse;
    }

    /**
     * @param string $adresse
     */
    public function setAdresse(string $adresse): void
    {
        $this->adresse = $adresse;
    }

    /**
     * @return string
     */
    public function getCodePostal(): string
    {
        return $this->codePostal;
    }

    /**
     * @param string $codePostal
     */
    public function setCodePostal(string $codePostal): void
    {
        $this->codePostal = $codePostal;
    }

    /**
     * @return string
     */
    public function getVille(): string
    {
        return $this->ville;
    }

    /**
     * @param string $ville
     */
    public function setVille(string $ville): void
    {
        $this->ville = $ville;
    }

    /**
     * @return ResponsableIut
     */
    public function getResponsableIut(): ResponsableIut
    {
        return $this->ResponsableIut;
    }

    /**
     * @param ResponsableIut $ResponsableIut
     */
    public function setResponsableIut(ResponsableIut $ResponsableIut): void
    {
        $this->ResponsableIut = $ResponsableIut;
    }

    /**
     * @return DossierApprenti
     */
    public function getDossierApprenti(): DossierApprenti
    {
        return $this->DossierApprenti;
    }

    /**
     * @param DossierApprenti $DossierApprenti
     */
    public function setDossierApprenti(DossierApprenti $DossierApprenti): void
    {
        $this->DossierApprenti = $DossierApprenti;
    }
}

