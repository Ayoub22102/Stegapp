<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Classe User
 * Classe de base pour les utilisateurs du système.
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap(['Client' => Client::class, 'Technicien' => Technicien::class, 'Chefequipe' => Chefequipe::class, 'Admin' => User::class])]
#[UniqueEntity(fields: ['username'], message: 'Un compte existe déjà avec ce nom d\'utilisateur')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $username = null; // Nom d'utilisateur pour l'authentification

    /**
     * @var list<string> Les rôles de l'utilisateur
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string Le mot de passe haché
     */
    #[ORM\Column]
    private ?string $password = null;

    // Récupère l'ID de l'utilisateur
    public function getId(): ?int
    {
        return $this->id;
    }

    // Récupère le nom d'utilisateur
    public function getUsername(): ?string
    {
        return $this->username;
    }

    // Définit le nom d'utilisateur
    public function setUsername(string $username): static
    {
        $this->username = $username;
        return $this;
    }

    // Identifiant visuel de l'utilisateur
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    // Retourne les rôles de l'utilisateur, en garantissant que ROLE_USER est toujours présent
    public function getRoles(): array
    {
        if (!in_array('ROLE_USER', $this->roles)) {
            $this->roles[] = 'ROLE_USER'; // Assigner ROLE_USER par défaut si absent
        }

        return array_unique($this->roles);
    }

    // Définit les rôles
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    // Retourne le mot de passe de l'utilisateur
    public function getPassword(): ?string
    {
        return $this->password;
    }

    // Définit le mot de passe
    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    // Effacer les données sensibles
    public function eraseCredentials(): void
    {
        // Effacer les données sensibles temporaires ici
    }
}
