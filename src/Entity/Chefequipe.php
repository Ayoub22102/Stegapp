<?php

namespace App\Entity;

use App\Repository\ChefequipeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Classe Chefequipe
 * Représente un chef d'équipe qui gère des réclamations et des techniciens.
 */
#[ORM\Entity(repositoryClass: ChefequipeRepository::class)]
class Chefequipe extends User
{
    #[ORM\Column(length: 50)]
    private ?string $nom = null; // Nom du chef d'équipe

    #[ORM\Column(length: 50)]
    private ?string $prenom = null; // Prénom du chef d'équipe

    #[ORM\Column]
    public ?int $cin = null; // Numéro de CIN du chef d'équipe

    #[ORM\Column]
    public ?int $telephone = null; // Numéro de téléphone du chef d'équipe

    // Récupère le nom du chef d'équipe
    public function getNom(): ?string
    {
        return $this->nom;
    }

    // Définit le nom du chef d'équipe
    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    // Récupère le prénom du chef d'équipe
    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    // Définit le prénom du chef d'équipe
    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    // Récupère le CIN du chef d'équipe
    public function getCin(): ?int
    {
        return $this->cin;
    }

    // Définit le CIN du chef d'équipe
    public function setCin(int $cin): static
    {
        $this->cin = $cin;
        return $this;
    }

    // Récupère le téléphone du chef d'équipe
    public function getTelephone(): ?int
    {
        return $this->telephone;
    }

    // Définit le téléphone du chef d'équipe
    public function setTelephone(int $telephone): static
    {
        $this->telephone = $telephone;
        return $this;
    }
}
