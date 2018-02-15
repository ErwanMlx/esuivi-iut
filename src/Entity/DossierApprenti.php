<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DossierApprenti
 *
 * @ORM\Table(name="dossier_apprenti", indexes={@ORM\Index(name="IDX_4F7776BA8937AB7", columns={"id_entreprise"}), @ORM\Index(name="IDX_4F7776BF72552AC", columns={"id_etape_actuelle"}), @ORM\Index(name="IDX_4F7776BF0FBA8F9", columns={"id_maitre_apprentissage"})})
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
    private $etat = 'En cours';

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
     * @ORM\ManyToOne(targetEntity="Entreprise")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_entreprise", referencedColumnName="id")
     * })
     */
    private $entreprise;

    /**
     * @var EtapeDossier
     *
     * @ORM\ManyToOne(targetEntity="EtapeDossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_etape_actuelle", referencedColumnName="id")
     * })
     */
    private $etapeActuelle;

    /**
     * @var Compte
     *
     * @ORM\ManyToOne(targetEntity="Compte")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_maitre_apprentissage", referencedColumnName="id")
     * })
     */
    private $maitreApprentissage;

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
    public function getSujetPropose(): ?string
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
    public function getEntreprise(): ?Entreprise
    {
        return $this->entreprise;
    }

    /**
     * @param Entreprise $entreprise
     */
    public function setEntreprise(Entreprise $entreprise): void
    {
        $this->entreprise = $entreprise;
    }

    /**
     * @return EtapeDossier
     */
    public function getEtapeActuelle(): EtapeDossier
    {
        return $this->etapeActuelle;
    }

    /**
     * @param EtapeDossier $etapeActuelle
     */
    public function setEtapeActuelle(EtapeDossier $etapeActuelle): void
    {
        $this->etapeActuelle = $etapeActuelle;
    }

    /**
     * @return Compte
     */
    public function getMaitreApprentissage(): Compte
    {
        return $this->maitreApprentissage;
    }

    /**
     * @param Compte $maitreApprentissage
     */
    public function setMaitreApprentissage(Compte $maitreApprentissage): void
    {
        $this->maitreApprentissage = $maitreApprentissage;
    }
}

