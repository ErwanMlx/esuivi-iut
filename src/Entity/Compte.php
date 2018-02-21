<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Compte
 *
 * @ORM\Table(name="compte")
 * @ORM\Entity(repositoryClass="App\Repository\CompteRepository")
 */
class Compte implements UserInterface, \Serializable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="compte_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=64, nullable=false)
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
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=64, nullable=false)
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
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=128, nullable=false)
     * @ORM\Column(name="email", type="string", length=128, nullable=false)
     * @Assert\Length(max = 128,
     *     maxMessage = "L'email doit faire moins de {{ limit }} caractères.",
     *     groups={"ajout"}
     *     )
     * @Assert\Email(message="L'email '{{ value }}' n'est pas un email correct.", groups={"ajout"})
     * @Assert\NotBlank(message="L'email ne peut pas être vide.", groups={"ajout"})
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=128, nullable=false)
     * @ORM\Column(name="password", type="string", length=128, nullable=false)
     * @Assert\Length(min = 8, max = 128,
     *     minMessage = "Le mot de passe doit faire au moins {{ limit }} caractères.",
     *     maxMessage = "Le mot de passe doit faire moins de {{ limit }} caractères."
     * )
     * @Assert\NotBlank(message="Le mot de passe ne peut pas être vide.")
     */
    private $password;


    /**
     * @ORM\Column(name="roles", type="json", nullable=true)
     */
    private $roles = [];

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
     * @return string
     */
    public function getEmail(): ?string
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
    public function getPassword(): ?string
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
     * Retourne les rôles de l'user
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        // Afin d'être sûr qu'un user a toujours au moins 1 rôle
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getUsername()
    {
        return $this->getEmail();
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * Retour le salt qui a servi à coder le mot de passe
     *
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        // See "Do you need to use a Salt?" at https://symfony.com/doc/current/cookbook/security/entity_provider.html
        // we're using bcrypt in security.yml to encode the password, so
        // the salt value is built-in and you don't have to generate one

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        return serialize([$this->id, $this->email, $this->password]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        [$this->id, $this->email, $this->password] = unserialize($serialized, ['allowed_classes' => false]);
    }
}

