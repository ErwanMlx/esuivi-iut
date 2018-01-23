<?php

namespace App\Entity;

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
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="Etape_Dossier_ID_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="ID_Type_Etape", type="integer", nullable=false)
     */
    private $idTypeEtape;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Date_Debut", type="date", nullable=false)
     */
    private $dateDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Date_Validation", type="date", nullable=false)
     */
    private $dateValidation;

    /**
     * @var integer
     *
     * @ORM\Column(name="ID_Validateur", type="integer", nullable=false)
     */
    private $idValidateur;

    /**
     * @var integer
     *
     * @ORM\Column(name="ID_Dossier", type="integer", nullable=false)
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
     * @return int
     */
    public function getIdTypeEtape(): int
    {
        return $this->idTypeEtape;
    }

    /**
     * @param int $idTypeEtape
     */
    public function setIdTypeEtape(int $idTypeEtape): void
    {
        $this->idTypeEtape = $idTypeEtape;
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
    public function getIdValidateur(): int
    {
        return $this->idValidateur;
    }

    /**
     * @param int $idValidateur
     */
    public function setIdValidateur(int $idValidateur): void
    {
        $this->idValidateur = $idValidateur;
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
}

