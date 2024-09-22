<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Classe Reclamation
 * Représente une réclamation soumise par un client.
 */
#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
#[ORM\InheritanceType('JOINED')] // Stratégie d'héritage JOINED
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap(['reclamation' => Reclamation::class, 'intervention' => Intervention::class])]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $type = null; // Type de réclamation

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null; // Description de la réclamation

    #[Gedmo\Timestampable(on:'create')]
    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_soumission = null; // Date de soumission de la réclamation

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $etat_validation = 'en attente'; // État de validation de la réclamation

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null; // Adresse où la réclamation a été faite

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $clientmail = null; // Email du client ayant soumis la réclamation

    /**
     * @var Collection<int, Technicien> Techniciens affectés à la réclamation
     */
    #[ORM\ManyToMany(targetEntity: Technicien::class, inversedBy: 'reclamations')]
    private Collection $affectation_technicien;

    #[ORM\Column(length: 50)]
    private ?string $ville = null;

    public function __construct()
    {
        $this->affectation_technicien = new ArrayCollection();
    }

    // Récupère l'ID de la réclamation
    public function getId(): ?int
    {
        return $this->id;
    }

    // Récupère le type de réclamation
    public function getType(): ?string
    {
        return $this->type;
    }

    // Définit le type de réclamation
    public function setType(?string $type): static
    {
        $this->type = $type;
        return $this;
    }

    // Récupère la description de la réclamation
    public function getDescription(): ?string
    {
        return $this->description;
    }

    // Définit la description de la réclamation
    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    // Récupère la date de soumission de la réclamation
    public function getDateSoumission(): ?\DateTimeInterface
    {
        return $this->date_soumission;
    }

    // Définit la date de soumission de la réclamation
    public function setDateSoumission(?\DateTimeInterface $date_soumission): static
    {
        $this->date_soumission = $date_soumission;
        return $this;
    }

    // Récupère l'état de validation
    public function getEtatValidation(): ?string
    {
        return $this->etat_validation;
    }

    // Définit l'état de validation
    public function setEtatValidation(?string $etat_validation): static
    {
        $this->etat_validation = $etat_validation;
        return $this;
    }

    // Récupère l'adresse liée à la réclamation
    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    // Définit l'adresse liée à la réclamation
    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;
        return $this;
    }

    // Récupère l'email du client
    public function getClientmail(): ?string
    {
        return $this->clientmail;
    }

    // Définit l'email du client
    public function setClientmail(?string $clientmail): static
    {
        $this->clientmail = $clientmail;
        return $this;
    }

    // Indique si la réclamation est acceptée
    public function isAccepted(): bool
    {
        return $this->etat_validation === 'acceptée';
    }

    // Récupère les techniciens affectés
    public function getAffectationTechnicien(): Collection
    {
        return $this->affectation_technicien;
    }

    // Ajoute un technicien à la réclamation
    public function addAffectationTechnicien(Technicien $affectationTechnicien): static
    {
        if (!$this->affectation_technicien->contains($affectationTechnicien)) {
            $this->affectation_technicien->add($affectationTechnicien);
        }

        return $this;
    }

    // Retire un technicien de la réclamation
    public function removeAffectationTechnicien(Technicien $affectationTechnicien): static
    {
        $this->affectation_technicien->removeElement($affectationTechnicien);

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }
}
