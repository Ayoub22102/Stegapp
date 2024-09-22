<?php

namespace App\Entity;

use App\Repository\TechnicienRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Classe Technicien
 * Représente un technicien du système.
 */
#[ORM\Entity(repositoryClass: TechnicienRepository::class)]
class Technicien extends User
{
    #[ORM\Column(length: 50)]
    private ?string $nom = null; // Nom du technicien

    #[ORM\Column(length: 50)]
    private ?string $prenom = null; // Prénom du technicien

    #[ORM\Column]
    public ?int $cin = null; // Numéro de CIN du technicien

    #[ORM\Column]
    public ?int $telephone = null; // Numéro de téléphone du technicien

    /**
     * @var Collection<int, Reclamation> Liste des réclamations affectées au technicien
     */
    #[ORM\ManyToMany(targetEntity: Reclamation::class, mappedBy: 'affectation_technicien')]
    private Collection $reclamations;

    public function __construct()
    {
        $this->reclamations = new ArrayCollection(); // Initialisation des réclamations
    }

    // Récupère le nom du technicien
    public function getNom(): ?string
    {
        return $this->nom;
    }

    // Définit le nom du technicien
    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    // Récupère le prénom du technicien
    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    // Définit le prénom du technicien
    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    // Récupère le CIN du technicien
    public function getCin(): ?int
    {
        return $this->cin;
    }

    // Définit le CIN du technicien
    public function setCin(int $cin): static
    {
        $this->cin = $cin;
        return $this;
    }

    // Récupère le téléphone du technicien
    public function getTelephone(): ?int
    {
        return $this->telephone;
    }

    // Définit le téléphone du technicien
    public function setTelephone(int $telephone): static
    {
        $this->telephone = $telephone;
        return $this;
    }

    // Récupère les réclamations affectées au technicien
    public function getReclamations(): Collection
    {
        return $this->reclamations;
    }

    // Ajoute une réclamation au technicien
    public function addReclamation(Reclamation $reclamation): static
    {
        if (!$this->reclamations->contains($reclamation)) {
            $this->reclamations->add($reclamation);
            $reclamation->addAffectationTechnicien($this);
        }

        return $this;
    }

    // Supprime une réclamation du technicien
    public function removeReclamation(Reclamation $reclamation): static
    {
        if ($this->reclamations->removeElement($reclamation)) {
            $reclamation->removeAffectationTechnicien($this);
        }

        return $this;
    }
}
