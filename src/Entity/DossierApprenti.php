<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DossierApprenti
 *
 * @ORM\Table(name="dossier_apprenti", indexes={@ORM\Index(name="IDX_4F7776BA8937AB7", columns={"id_entreprise"}), @ORM\Index(name="IDX_4F7776BF0FBA8F9", columns={"id_maitre_apprentissage"}), @ORM\Index(name="IDX_4F7776BF72552AC", columns={"id_etape_actuelle"})})
 * @ORM\Entity(repositoryClass="App\Repository\DossierApprentiRepository")
 */
class DossierApprenti
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="dossier_apprenti_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="etat", type="string", length=16, nullable=false)
     */
    private $etat;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_embauche", type="date", nullable=true)
     */
    private $dateEmbauche;

    /**
     * @var string
     *
     * @ORM\Column(name="sujet_propose", type="string", length=128, nullable=true)
     */
    private $sujetPropose;

    /**
     * @var string
     *
     * @ORM\Column(name="description_du_sujet", type="string", length=512, nullable=true)
     */
    private $descriptionDuSujet;

    /**
     * @var integer
     *
     * @ORM\Column(name="participation_financiere", type="integer", nullable=true)
     */
    private $participationFinanciere;

    /**
     * @var Entreprise
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Entreprise")
     * @ORM\JoinColumn(name="id_entreprise", referencedColumnName="id")
     */
    private $Entreprise;

    /**
     * @var MaitreApprentissage
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\MaitreApprentissage")
     * @ORM\JoinColumn(name="id_maitre_apprentissage", referencedColumnName="id")
     */
    private $MaitreApprentissage;

    /**
     * @var EtapeDossier
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\EtapeDossier")
     * @ORM\JoinColumn(name="id_etape_actuelle", referencedColumnName="id")
     */
    private $EtapeActuelle;

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
    public function getEtat(): string
    {
        return $this->etat;
    }

    /**
     * @param string $etat
     */
    public function setEtat(string $etat): void
    {
        $this->etat = $etat;
    }

    /**
     * @return \DateTime
     */
    public function getDateEmbauche(): \DateTime
    {
        return $this->dateEmbauche;
    }

    /**
     * @param \DateTime $dateEmbauche
     */
    public function setDateEmbauche(\DateTime $dateEmbauche): void
    {
        $this->dateEmbauche = $dateEmbauche;
    }

    /**
     * @return string
     */
    public function getSujetPropose(): string
    {
        return $this->sujetPropose;
    }

    /**
     * @param string $sujetPropose
     */
    public function setSujetPropose(string $sujetPropose): void
    {
        $this->sujetPropose = $sujetPropose;
    }

    /**
     * @return string
     */
    public function getDescriptionDuSujet(): string
    {
        return $this->descriptionDuSujet;
    }

    /**
     * @param string $descriptionDuSujet
     */
    public function setDescriptionDuSujet(string $descriptionDuSujet): void
    {
        $this->descriptionDuSujet = $descriptionDuSujet;
    }

    /**
     * @return int
     */
    public function getParticipationFinanciere(): int
    {
        return $this->participationFinanciere;
    }

    /**
     * @param int $participationFinanciere
     */
    public function setParticipationFinanciere(int $participationFinanciere): void
    {
        $this->participationFinanciere = $participationFinanciere;
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

    /**
     * @return MaitreApprentissage
     */
    public function getMaitreApprentissage(): MaitreApprentissage
    {
        return $this->MaitreApprentissage;
    }

    /**
     * @param MaitreApprentissage $MaitreApprentissage
     */
    public function setMaitreApprentissage(MaitreApprentissage $MaitreApprentissage): void
    {
        $this->MaitreApprentissage = $MaitreApprentissage;
    }

    /**
     * @return EtapeDossier
     */
    public function getEtapeActuelle(): EtapeDossier
    {
        return $this->EtapeActuelle;
    }

    /**
     * @param EtapeDossier $EtapeActuelle
     */
    public function setEtapeActuelle(EtapeDossier $EtapeActuelle): void
    {
        $this->EtapeActuelle = $EtapeActuelle;
    }
}

