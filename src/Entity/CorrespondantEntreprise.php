<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CorrespondantEntreprise
 *
 * @ORM\Table(name="correspondant_entreprise", options={"comment":"Les informations des correspondants des entreprises des apprentis"})
 * @ORM\Entity(repositoryClass="App\Repository\CorrespondantEntrepriseRepository")
 */
class CorrespondantEntreprise
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="correspondant_entreprise_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=32, nullable=false)
     * @Assert\NotBlank(message="Le nom du correspondant ne peut pas être vide.")
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=32, nullable=false)
     * @Assert\NotBlank(message="Le prénom du correspondant ne peut pas être vide.")
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="fonction", type="string", length=256, nullable=false)
     * @Assert\NotBlank(message="La fonction du correspondant ne peut pas être vide.")
     */
    private $fonction;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=10, nullable=false)
     * @Assert\Length(min = 10, max = 10,
     *     exactMessage = "Le numéro de téléphone du correspondant doit faire {{ limit }} caractères.")
     * @Assert\NotBlank(message="Le numéro de téléphone du correspondant ne peut pas être vide.")
     * @Assert\Type(
     *     type="numeric",
     *     message="Le téléphone du correspondant ne doit contenir que des chiffres."
     * )
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=256, nullable=false)
     * @Assert\NotBlank(message="L'email du correspondant ne peut pas être vide.")
     */
    private $email;

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
    public function getPrenom()
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
    public function getFonction()
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

}

