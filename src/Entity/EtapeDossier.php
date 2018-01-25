<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping as ORM;

/**
 * etapedossier
 *
 * @ORM\Table(name="etape_dossier")
 * @ORM\Entity(repositoryClass="App\Repository\EtapeDossierRepository")
 */
class EtapeDossier
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="Etape_Dossier_ID_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TypeEtape")
     * @ORM\JoinColumn(name="id_type_etape", referencedColumnName="id")
     */
    private $TypeEtape;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_debut", type="date", nullable=false)
     */
    private $dateDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_validation", type="date", nullable=false)
     */
    private $dateValidation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ResponsableCfa")
     * @ORM\JoinColumn(name="id_validateur_cfa", referencedColumnName="id", nullable=true)
     */
    private $ValidateurCfa;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ResponsableIut")
     * @ORM\JoinColumn(name="id_validateur_iut", referencedColumnName="id", nullable=true)
     */
    private $ValidateurIut;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_dossier", type="integer", nullable=false)
     */
    private $idDossier;

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
     * @return mixed
     */
    public function getTypeEtape(): TypeEtape
    {
        return $this->TypeEtape;
    }

    /**
     * @param mixed $TypeEtape
     */
    public function setTypeEtape(TypeEtape $TypeEtape)
    {
        $this->TypeEtape = $TypeEtape;
    }

    /**
     * @return \DateTime
     */
    public function getDateDebut(): \DateTime
    {
        return $this->dateDebut;
    }

    /**
     * @param \DateTime $dateDebut
     */
    public function setDateDebut(\DateTime $dateDebut): void
    {
        $this->dateDebut = $dateDebut;
    }

    /**
     * @return \DateTime
     */
    public function getDateValidation(): \DateTime
    {
        return $this->dateValidation;
    }

    /**
     * @param \DateTime $dateValidation
     */
    public function setDateValidation(\DateTime $dateValidation): void
    {
        $this->dateValidation = $dateValidation;
    }

    /**
     * @return int
     */
    public function getIdDossier(): int
    {
        return $this->idDossier;
    }

    /**
     * @param int $idDossier
     */
    public function setIdDossier(int $idDossier): void
    {
        $this->idDossier = $idDossier;
    }

    public function getValidateurCfa(): ?ResponsableCfa
    {
        return $this->ValidateurCfa;
    }

    public function setValidateurCfa(ResponsableCfa $ValidateurCfa): void
    {
        $this->ValidateurCfa = $ValidateurCfa;
    }

    public function getValidateurIut(): ?ResponsableIut
    {
        return $this->ValidateurIut;
    }

    public function setValidateurIut(ResponsableIut $ValidateurIut): void
    {
        $this->ValidateurIut = $ValidateurIut;
    }
}

