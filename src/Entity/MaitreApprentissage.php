<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MaitreApprentissage
 *
 * @ORM\Table(name="maitre_apprentissage", indexes={@ORM\Index(name="IDX_FC0B595DA8937AB7", columns={"id_entreprise"})})
 * @ORM\Entity(repositoryClass="App\Repository\MaitreApprentissageRepository")
 */
class MaitreApprentissage
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="maitre_apprentissage_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=64, nullable=false)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=64, nullable=false)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="fonction", type="string", length=256, nullable=false)
     */
    private $fonction;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=10, nullable=false)
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="fax", type="string", length=10, nullable=true)
     */
    private $fax;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=256, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=64, nullable=false)
     */
    private $password;

    /**
     * @var Entreprise
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Entreprise")
     * @ORM\JoinColumn(name="id_entreprise", referencedColumnName="id")
     */
    private $Entreprise;

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
    public function getNom(): string
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
    public function getPrenom(): string
    {
        return $this->prenom;
    }

    /**
     * @param string $prenom
     */
    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }

    /**
     * @return string
     */
    public function getFonction(): string
    {
        return $this->fonction;
    }

    /**
     * @param string $fonction
     */
    public function setFonction(string $fonction): void
    {
        $this->fonction = $fonction;
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
    public function getFax(): string
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
    public function getEmail(): string
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
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return Entreprise
     */
    public function getEntreprise(): Entreprise
    {
        return $this->Entreprise;
    }

    /**
     * @param Entreprise $Entreprise
     */
    public function setEntreprise(Entreprise $Entreprise): void
    {
        $this->Entreprise = $Entreprise;
    }
}

