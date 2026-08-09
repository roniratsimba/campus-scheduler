<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Entité User - Utilisateur du système
 * 
 * Représente un utilisateur pouvant s'authentifier dans l'application.
 * Implémente les interfaces Symfony Security pour l'authentification.
 * 
 * @author Campus Scheduler Team
 * @version 1.0
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * Identifiant unique de l'utilisateur
     * Généré automatiquement par la base de données
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Adresse email de l'utilisateur (unique)
     * Utilisée comme identifiant pour l'authentification
     */
    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    /**
     * Mot de passe hashé de l'utilisateur
     * Stocké après hachage avec password_hash()
     */
    #[ORM\Column(length: 255)]
    private ?string $password = null;

    /**
     * Rôle de l'utilisateur dans le système
     * Ex: ROLE_ADMIN, ROLE_USER
     */
    #[ORM\Column(length: 50)]
    private ?string $role = null;

    /**
     * Date et heure de création du compte
     * Définie automatiquement à la création
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * Date et heure de dernière mise à jour
     * Null si jamais modifié
     */
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * Constructeur - Initialise la date de création
     * Appelé automatiquement lors de l'instanciation
     */
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * Retourne l'identifiant de l'utilisateur
     * @return int|null L'identifiant unique
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne l'email de l'utilisateur
     * @return string|null L'adresse email
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Définit l'email de l'utilisateur
     * @param string $email La nouvelle adresse email
     * @return static L'instance courante (fluent interface)
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Retourne le mot de passe hashé
     * @return string|null Le mot de passe hashé
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Définit le mot de passe (doit être hashé avant)
     * @param string $password Le mot de passe hashé
     * @return static L'instance courante
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Retourne le rôle de l'utilisateur
     * @return string|null Le rôle (ex: ROLE_ADMIN)
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * Définit le rôle de l'utilisateur
     * @param string $role Le nouveau rôle
     * @return static L'instance courante
     */
    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Retourne la date de création
     * @return \DateTimeImmutable|null La date de création
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Définit la date de création
     * @param \DateTimeImmutable $createdAt La nouvelle date
     * @return static L'instance courante
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Retourne la date de dernière mise à jour
     * @return \DateTimeImmutable|null La date de mise à jour
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Définit la date de dernière mise à jour
     * @param \DateTimeImmutable|null $updatedAt La nouvelle date ou null
     * @return static L'instance courante
     */
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Retourne l'identifiant utilisé pour l'authentification
     * Requis par l'interface UserInterface
     * @return string L'email comme identifiant
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * Retourne les rôles de l'utilisateur
     * Requis par l'interface UserInterface
     * @return array Tableau des rôles
     */
    public function getRoles(): array
    {
        return [$this->role];
    }

    /**
     * Efface les données sensibles temporaires
     * Requis par l'interface UserInterface
     * Non utilisé dans cette implémentation
     */
    public function eraseCredentials(): void
    {
        // Si vous stockez des données temporaires sensibles, effacez-les ici
    }
}
