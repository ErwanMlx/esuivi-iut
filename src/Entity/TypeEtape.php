<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * "typeEtape"
 *
 * @ORM\Table(name="type_etape")
 * @ORM\Entity(repositoryClass="App\Repository\TypeEtapeRepository")
 */
class TypeEtape
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="type_etape_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_etape", type="string", length=32, nullable=false)
     */
    private $nomEtape;

    /**
     * @var string
     *
     * @ORM\Column(name="type_validateur", type="string", length=3, nullable=false)
     */
    private $typeValidateur;

    /**
     * @var string
     *
     * @ORM\Column(name="type_icone", type="string", length=100, nullable=true)
     */
    private $typeIcone;

    /**
     *
     * @ORM\OneToOne(targetEntity="App\Entity\TypeEtape")
     * @ORM\JoinColumn(name="id_type_etape_suivante", referencedColumnName="id")

     */
    private $typeEtapeSuivante;

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
    public function getNomEtape(): string
    {
        return $this->nomEtape;
    }

    /**
     * @param string $nomEtape
     */
    public function setNomEtape(string $nomEtape): void
    {
        $this->nomEtape = $nomEtape;
    }

    /**
     * @return string
     */
    public function getTypeValidateur(): string
    {
        return $this->typeValidateur;
    }

    /**
     * @param string $typeValidateur
     */
    public function setTypeValidateur(string $typeValidateur): void
    {
        $this->typeValidateur = $typeValidateur;
    }

    /**
     * @return string
     */
    public function getTypeIcone(): string
    {
        return $this->typeIcone;
    }

    /**
     * @param string $typeIcone
     */
    public function setTypeIcone(string $typeIcone): void
    {
        $this->typeIcone = $typeIcone;
    }


    /**
     * @return TypeEtape
     */
    public function getTypeEtapeSuivante(): ?TypeEtape
    {
        return $this->typeEtapeSuivante;
    }

    /**
     * @param TypeEtape $typeEtapeSuivante
     */
    public function setTypeEtapeSuivante(TypeEtape $typeEtapeSuivante): void
    {
        $this->typeEtapeSuivante = $typeEtapeSuivante;
    }
}

