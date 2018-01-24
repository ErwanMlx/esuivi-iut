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
     * @var integer
     *
     * @ORM\Column(name="id_etape_suivante", type="integer", nullable=true)
     */
    private $idEtapeSuivante;

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
     * @return int
     */
    public function getIdEtapeSuivante(): int
    {
        return $this->idEtapeSuivante;
    }

    /**
     * @param int $idEtapeSuivante
     */
    public function setIdEtapeSuivante(int $idEtapeSuivante): void
    {
        $this->idEtapeSuivante = $idEtapeSuivante;
    }
}

