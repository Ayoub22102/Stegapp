<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Classe Client
 * Représente un client du système.
 */
#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client extends User
{
    #[ORM\Column(length: 50)]
    public ?string $nom = null; // Nom du client

    #[ORM\Column(length: 20)]
    public ?string $prenom = null; // Prénom du client

    #[ORM\Column]
    public ?int $cin = null; // Numéro de CIN du client

    #[ORM\Column]
    public ?int $telephone = null; // Numéro de téléphone du client

    // Récupère le nom du client
    public function getNom(): ?string
    {
        return $this->nom;
    }

    // Définit le nom du client
    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    // Récupère le prénom du client
    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    // Définit le prénom du client
    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    // Récupère le CIN du client
    public function getCin(): ?int
    {
        return $this->cin;
    }

    // Définit le CIN du client
    public function setCin(int $cin): static
    {
        $this->cin = $cin;
        return $this;
    }

    // Récupère le téléphone du client
    public function getTelephone(): ?int
    {
        return $this->telephone;
    }

    // Définit le téléphone du client
    public function setTelephone(int $telephone): static
    {
        $this->telephone = $telephone;
        return $this;
    }
}
