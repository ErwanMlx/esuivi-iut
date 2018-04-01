<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entreprise
 *
 * @ORM\Table(name="entreprise", options={"comment":"Les informations des entreprises des apprentis"}, indexes={@ORM\Index(name="IDX_D19FA608546CFD5", columns={"id_correspondant_entreprise"})})
 * @ORM\Entity(repositoryClass="App\Repository\EntrepriseRepository")
 */
class Entreprise
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="entreprise_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=64, nullable=false)
     * @Assert\NotBlank(message="Le nom de l'entreprise ne peut pas être vide.")
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="raison_sociale", type="string", length=128, nullable=true)
     */
    private $raisonSociale;

    /**
     * @var string
     *
     * @ORM\Column(name="siret", type="string", length=14, nullable=true)
     */
    private $siret;

    /**
     * @var integer
     *
     * @ORM\Column(name="nombre_salaries", type="integer", nullable=true)
     */
    private $nombreSalaries;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=256, nullable=false)
     * @Assert\Length(max = 256,
     *     maxMessage = "L'adresse doit faire moins de {{ limit }} caractères.")
     * @Assert\NotBlank(message="L'adresse ne peut pas être vide.")
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="code_postal", type="string", length=5, nullable=true)
     * @Assert\Length(min = 5, max = 5,
     *     exactMessage = "Le code postal doit faire moins de {{ limit }} caractères.")
     * @Assert\NotBlank(message="Le code postal ne peut pas être vide.")
     * @Assert\Type(
     *     type="numeric",
     *     message="Le code postal ne doit contenir que des chiffres."
     * )
     */
    private $codePostal;

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=64, nullable=false)
     * @Assert\Length(max = 64,
     *     maxMessage = "La ville doit faire moins de {{ limit }} caractères.")
     * @Assert\NotBlank(message="La ville ne peut pas être vide.")
     * @Assert\Type(
     *     type="string",
     *     message="La ville ne doit contenir que des lettres."
     * )
     */
    private $ville;

    /**
     * @var string
     *
     * @ORM\Column(name="fax", type="string", length=10, nullable=true)
     */
    private $fax;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=128, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="domaine_activite", type="string", length=256, nullable=true)
     */
    private $domaineActivite;

    /**
     * @var CorrespondantEntreprise
     *
     * @ORM\ManyToOne(targetEntity="CorrespondantEntreprise")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_correspondant_entreprise", referencedColumnName="id")
     * })
//     * @Assert\Valid()
     */
    private $correspondantEntreprise;

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
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     */
    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @return string
     */
    public function getRaisonSociale()
    {
        return $this->raisonSociale;
    }

    /**
     * @param string $raisonSociale
     */
    public function setRaisonSociale(string $raisonSociale): void
    {
        $this->raisonSociale = $raisonSociale;
    }

    /**
     * @return string
     */
    public function getSiret()
    {
        return $this->siret;
    }

    /**
     * @param string $siret
     */
    public function setSiret(string $siret): void
    {
        $this->siret = $siret;
    }

    /**
     * @return int
     */
    public function getNombreSalaries()
    {
        return $this->nombreSalaries;
    }

    /**
     * @param int $nombreSalaries
     */
    public function setNombreSalaries(int $nombreSalaries): void
    {
        $this->nombreSalaries = $nombreSalaries;
    }

    /**
     * @return string
     */
    public function getAdresse()
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
    public function getCodePostal()
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
    public function getVille()
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
     * @return string
     */
    public function getTelephone()
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
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * @param string $fax
     */
    public function setFax(string $fax): void
    {
        $this->fax = $fax;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getDomaineActivite()
    {
        return $this->domaineActivite;
    }

    /**
     * @param string $domaineActivite
     */
    public function setDomaineActivite(string $domaineActivite): void
    {
        $this->domaineActivite = $domaineActivite;
    }

    /**
     * @return CorrespondantEntreprise
     */
    public function getCorrespondantEntreprise(): CorrespondantEntreprise
    {
        return $this->correspondantEntreprise;
    }

    /**
     * @param CorrespondantEntreprise $correspondantEntreprise
     */
    public function setCorrespondantEntreprise(CorrespondantEntreprise $correspondantEntreprise): void
    {
        $this->correspondantEntreprise = $correspondantEntreprise;
    }


}

