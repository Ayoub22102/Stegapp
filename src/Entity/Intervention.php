<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\InterventionRepository;
use Doctrine\DBAL\Types\Types;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Classe Intervention
 * Représente une intervention liée à une réclamation.
 */
#[ORM\Entity(repositoryClass: InterventionRepository::class)]
#[ORM\Table(name: 'intervention')]
class Intervention extends Reclamation
{
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $analyse = null; // Analyse effectuée par le technicien

    #[Gedmo\Timestampable(on:'create')]
    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]

    private ?\DateTimeInterface $date = null; // Date de l'intervention

    #[ORM\Column(length: 50)]
    private ?string $etat = "en cours"; // État de l'intervention

    // Récupère l'analyse effectuée
    public function getAnalyse(): ?string
    {
        return $this->analyse;
    }

    // Définit l'analyse effectuée
    public function setAnalyse(string $analyse): static
    {
        $this->analyse = $analyse;
        return $this;
    }

    // Récupère la date de l'intervention
    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    // Définit la date de l'intervention
    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;
        return $this;
    }

    // Récupère l'état de l'intervention
    public function getEtat(): ?string
    {
        return $this->etat;
    }

    // Définit l'état de l'intervention
    public function setEtat(string $etat): static
    {
        $this->etat = $etat;
        return $this;
    }
    
}
