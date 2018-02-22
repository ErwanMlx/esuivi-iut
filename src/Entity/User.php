<?php

namespace App\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="utilisateur")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=64, nullable=true)
     * @Assert\Length(max = 64,
     *     maxMessage = "Le nom doit faire moins de {{ limit }} caractères.",
     *     groups={"ajout"}
     *     )
     * @Assert\NotBlank(message="Le nom ne peut pas être vide.", groups={"ajout"})
     * @Assert\Type(
     *     type="string",
     *     message="Le nom ne doit contenir que des lettres."
     * )
     */
    protected $nom;

    /**
     * @return string
     */
    public function getNom(): ?string
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
    public function getPrenom(): ?string
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
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=64, nullable=true)
     * @Assert\Length(max = 64,
     *     maxMessage = "Le prénom doit faire moins de {{ limit }} caractères.",
     *     groups={"ajout"}
     *     )
     * @Assert\NotBlank(message="Le prénom ne peut pas être vide.", groups={"ajout"})
     * @Assert\Type(
     *     type="string",
     *     message="Le prénom ne doit contenir que des lettres."
     * )
     */
    protected $prenom;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    public function setEmail($email)
    {
        $email = is_null($email) ? '' : $email;
        parent::setEmail($email);
        $this->setUsername($email);

        return $this;
    }
}