<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


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
     * @Assert\Length(max = 256,
     *     maxMessage = "L'adresse doit faire moins de {{ limit }} caractères.")
     * @Assert\NotBlank(message="L'adresse ne peut pas être vide.")
     */
    private $adresse;

    /**
     * @var integer
     *
     * @ORM\Column(name="code_postal", type="string", length=5, nullable=true)
     * @Assert\Length(max = 5,
     *     maxMessage = "Le code postal doit faire moins de {{ limit }} caractères.")
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

