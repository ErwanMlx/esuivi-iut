<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResponsableCfa
 *
 * @ORM\Table(name="responsable_cfa")
 * @ORM\Entity(repositoryClass="App\Repository\ResponsableCfa")
 */
class ResponsableCfa extends Compte
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="responsable_cfa_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var ResponsableIut
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ResponsableIut")
     * @ORM\JoinColumn(name="id_responsable_iut", referencedColumnName="id")
     */
    private $ResponsableIut;

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

}

