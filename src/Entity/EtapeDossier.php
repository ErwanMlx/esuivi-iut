<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * EtapeDossier
 *
 * @ORM\Table(name="etape_dossier", options={"comment":"Les étapes du dossier de l'apprenti"}, indexes={@ORM\Index(name="IDX_662143ECE3D54947", columns={"id_dossier"}), @ORM\Index(name="IDX_662143EC1E108449", columns={"id_validateur"}), @ORM\Index(name="IDX_662143EC83D972FD", columns={"id_type_etape"})})
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
     * @ORM\SequenceGenerator(sequenceName="etape_dossier_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_debut", type="datetime", nullable=false)
     */
    private $dateDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_validation", type="datetime", nullable=true)
     */
    private $dateValidation;

    /**
     * @var DossierApprenti
     *
     * @ORM\ManyToOne(targetEntity="DossierApprenti")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_dossier", referencedColumnName="id")
     * })
     */
    private $dossier;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_validateur", referencedColumnName="id")
     * })
     */
    private $validateur;

    /**
     * @var TypeEtape
     *
     * @ORM\ManyToOne(targetEntity="TypeEtape")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_type_etape", referencedColumnName="id")
     * })
     */
    private $typeEtape;

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
    public function getDateValidation() : ?\DateTime
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
     * @return DossierApprenti
     */
    public function getDossier(): DossierApprenti
    {
        return $this->dossier;
    }

    /**
     * @param DossierApprenti $dossier
     */
    public function setDossier(DossierApprenti $dossier): void
    {
        $this->dossier = $dossier;
    }

    /**
     * @return User
     */
    public function getValidateur(): User
    {
        return $this->validateur;
    }

    /**
     * @param User $validateur
     */
    public function setValidateur(User $validateur): void
    {
        $this->validateur = $validateur;
    }

    /**
     * @return TypeEtape
     */
    public function getTypeEtape(): TypeEtape
    {
        return $this->typeEtape;
    }

    /**
     * @param TypeEtape $typeEtape
     */
    public function setTypeEtape(TypeEtape $typeEtape): void
    {
        $this->typeEtape = $typeEtape;
    }

    public function __construct()
    {
        $this->dateDebut = new \DateTime();
    }

}

