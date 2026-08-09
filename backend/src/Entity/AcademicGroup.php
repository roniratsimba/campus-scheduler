<?php

namespace App\Entity;

use App\Repository\AcademicGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité AcademicGroup - Groupe académique d'étudiants
 * 
 * Représente un groupe d'étudiants défini par un niveau (L1, L2, etc.),
 * un programme d'études et un numéro de groupe.
 * Un groupe peut être associé à plusieurs séances de cours.
 * 
 * @author Campus Scheduler Team
 * @version 1.0
 */
#[ORM\Entity(repositoryClass: AcademicGroupRepository::class)]
#[ORM\Table(
    name: 'academic_group',
    uniqueConstraints: [
        new ORM\UniqueConstraint(
            name: 'uniq_academic_group',
            columns: ['level_id', 'program_id', 'group_number']
        )
    ]
)]
class AcademicGroup
{
    /**
     * Identifiant unique du groupe
     * Généré automatiquement par la base de données
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Numéro du groupe (ex: 1, 2, 3)
     * Combiné avec level et program pour former un identifiant unique
     */
    #[ORM\Column]
    private ?int $groupNumber = null;

    /**
     * Niveau académique du groupe (ex: L1, L2, M1)
     * Relation ManyToOne vers l'entité Level
     */
    #[ORM\ManyToOne(inversedBy: 'academicGroups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Level $level = null;

    /**
     * Programme d'études du groupe (ex: GB, SR, IA)
     * Relation ManyToOne vers l'entité Program
     */
    #[ORM\ManyToOne(inversedBy: 'academicGroups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Program $program = null;

    /**
     * Collection des séances de cours associées à ce groupe
     * Relation ManyToMany avec CourseSession
     * @var Collection<int, CourseSession>
     */
    #[ORM\ManyToMany(targetEntity: CourseSession::class, mappedBy: 'academicGroups')]
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
     * Retourne l'identifiant du groupe
     * @return int|null L'identifiant unique
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne le numéro du groupe
     * @return int|null Le numéro de groupe
     */
    public function getGroupNumber(): ?int
    {
        return $this->groupNumber;
    }

    /**
     * Définit le numéro du groupe
     * @param int $groupNumber Le nouveau numéro
     * @return static L'instance courante
     */
    public function setGroupNumber(int $groupNumber): static
    {
        $this->groupNumber = $groupNumber;

        return $this;
    }

    /**
     * Retourne le niveau académique
     * @return Level|null Le niveau (L1, L2, etc.)
     */
    public function getLevel(): ?Level
    {
        return $this->level;
    }

    /**
     * Définit le niveau académique
     * @param Level|null $level Le nouveau niveau
     * @return static L'instance courante
     */
    public function setLevel(?Level $level): static
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Retourne le programme d'études
     * @return Program|null Le programme (GB, SR, etc.)
     */
    public function getProgram(): ?Program
    {
        return $this->program;
    }

    /**
     * Définit le programme d'études
     * @param Program|null $program Le nouveau programme
     * @return static L'instance courante
     */
    public function setProgram(?Program $program): static
    {
        $this->program = $program;

        return $this;
    }

    /**
     * Retourne toutes les séances de cours du groupe
     * @return Collection<int, CourseSession> Collection des séances
     */
    public function getCourseSessions(): Collection
    {
        return $this->courseSessions;
    }

    /**
     * Ajoute une séance de cours au groupe
     * Maintient la relation bidirectionnelle
     * @param CourseSession $courseSession La séance à ajouter
     * @return static L'instance courante
     */
    public function addCourseSession(CourseSession $courseSession): static
    {
        if (!$this->courseSessions->contains($courseSession)) {
            $this->courseSessions->add($courseSession);
            $courseSession->addAcademicGroup($this);
        }

        return $this;
    }

    /**
     * Retire une séance de cours du groupe
     * Maintient la relation bidirectionnelle
     * @param CourseSession $courseSession La séance à retirer
     * @return static L'instance courante
     */
    public function removeCourseSession(CourseSession $courseSession): static
    {
        if ($this->courseSessions->removeElement($courseSession)) {
            $courseSession->removeAcademicGroup($this);
        }

        return $this;
    }
}
