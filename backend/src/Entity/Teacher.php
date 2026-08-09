<?php

namespace App\Entity;

use App\Repository\TeacherRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité Teacher - Enseignant
 * 
 * Représente un enseignant du système avec ses informations personnelles
 * et ses séances de cours associées.
 * 
 * @author Campus Scheduler Team
 * @version 1.0
 */
#[ORM\Entity(repositoryClass: TeacherRepository::class)]
class Teacher
{
    /**
     * Identifiant unique de l'enseignant
     * Généré automatiquement par la base de données
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Prénom de l'enseignant
     */
    #[ORM\Column(length: 100)]
    private ?string $firstName = null;

    /**
     * Nom de famille de l'enseignant (optionnel)
     */
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $lastName = null;

    /**
     * Email de contact de l'enseignant (unique)
     */
    #[ORM\Column(length: 255, unique:true)]
    private ?string $email = null;

    /**
     * Indique si l'enseignant est actif dans le système
     * Permet de désactiver un enseignant sans le supprimer
     */
    #[ORM\Column]
    private ?bool $isActive = null;

    /**
     * Collection des séances de cours assurées par cet enseignant
     * Relation OneToMany vers CourseSession
     * @var Collection<int, CourseSession>
     */
    #[ORM\OneToMany(targetEntity: CourseSession::class, mappedBy: 'teacher')]
    private Collection $courseSessions;

    /**
     * Constructeur - Initialise la collection de séances de cours
     * Appelé automatiquement lors de l'instanciation
     */
    public function __construct()
    {
        $this->courseSessions = new ArrayCollection();
    }

    /**
     * Retourne l'identifiant de l'enseignant
     * @return int|null L'identifiant unique
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne le prénom de l'enseignant
     * @return string|null Le prénom
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * Définit le prénom de l'enseignant
     * @param string $firstName Le nouveau prénom
     * @return static L'instance courante
     */
    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Retourne le nom de famille de l'enseignant
     * @return string|null Le nom (peut être null)
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * Définit le nom de famille de l'enseignant
     * @param string|null $lastName Le nouveau nom ou null
     * @return static L'instance courante
     */
    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Retourne l'email de l'enseignant
     * @return string|null L'adresse email
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Définit l'email de l'enseignant
     * @param string $email La nouvelle adresse email
     * @return static L'instance courante
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Vérifie si l'enseignant est actif
     * @return bool|null True si actif, false sinon
     */
    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    /**
     * Définit le statut actif/inactif de l'enseignant
     * @param bool $isActive Le nouveau statut
     * @return static L'instance courante
     */
    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Retourne toutes les séances de cours de l'enseignant
     * @return Collection<int, CourseSession> Collection des séances
     */
    public function getCourseSessions(): Collection
    {
        return $this->courseSessions;
    }

    /**
     * Ajoute une séance de cours à l'enseignant
     * Maintient la relation bidirectionnelle
     * @param CourseSession $courseSession La séance à ajouter
     * @return static L'instance courante
     */
    public function addCourseSession(CourseSession $courseSession): static
    {
        if (!$this->courseSessions->contains($courseSession)) {
            $this->courseSessions->add($courseSession);
            $courseSession->setTeacher($this);
        }

        return $this;
    }

    /**
     * Retire une séance de cours de l'enseignant
     * Maintient la relation bidirectionnelle
     * @param CourseSession $courseSession La séance à retirer
     * @return static L'instance courante
     */
    public function removeCourseSession(CourseSession $courseSession): static
    {
        if ($this->courseSessions->removeElement($courseSession)) {
            // Définit le côté owning à null (sauf si déjà changé)
            if ($courseSession->getTeacher() === $this) {
                $courseSession->setTeacher(null);
            }
        }

        return $this;
    }
}
