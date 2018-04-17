<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Apprenti
 *
 * @ORM\Table(name="apprenti", options={"comment":"Les informations sur les apprentis"}, indexes={@ORM\Index(name="IDX_2CB7951CE77D22BA", columns={"id_dossier_apprenti"}), @ORM\Index(name="IDX_2CB7951C81B56FBD", columns={"id_responsable_iut"})})
 * @ORM\Entity(repositoryClass="App\Repository\ApprentiRepository")
 */
class Apprenti
{
    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=10, nullable=true)
     * @Assert\Length(min = 10, max = 10,
     *     exactMessage = "Le numéro de téléphone doit faire {{ limit }} caractères.")
     * @Assert\NotBlank(message="Le numéro de téléphone ne peut pas être vide.")
     * @Assert\Type(
     *     type="numeric",
     *     message="Le téléphone ne doit contenir que des chiffres."
     * )
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=256, nullable=true)
     * @ORM\Column(name="adresse", type="string", length=256, nullable=true)
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
     * @ORM\Column(name="ville", type="string", length=64, nullable=true)
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
     * @var DossierApprenti
     *
     * @ORM\ManyToOne(targetEntity="DossierApprenti")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_dossier_apprenti", referencedColumnName="id")
     * })
     */
    private $dossier;

    /**
     * @var User
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_compte", referencedColumnName="id")
     * })
     * @Assert\Valid()
     */
    private $compte;

    /**
     * @return string
     */
    public function getTelephone(): ?string
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
    public function getAdresse(): ?string
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
    public function getCodePostal(): ?string
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
    public function getVille(): ?string
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
     * @return DossierApprenti
     */
    public function getDossier(): DossierApprenti
    {
        return $this->dossier;
    }

    /**
     * @param DossierApprenti $dossierApprenti
     */
    public function setDossierApprenti(DossierApprenti $dossierApprenti): void
    {
        $this->dossier = $dossierApprenti;
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

