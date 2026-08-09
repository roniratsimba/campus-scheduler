<?php

namespace App\Entity;

use App\Repository\SubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité Subject - Matière enseignée
 * 
 * Représente une matière ou discipline enseignée dans l'établissement.
 * Chaque matière peut être enseignée dans plusieurs séances de cours.
 * 
 * @author Campus Scheduler Team
 * @version 1.0
 */
#[ORM\Entity(repositoryClass: SubjectRepository::class)]
class Subject
{
    /**
     * Identifiant unique de la matière
     * Généré automatiquement par la base de données
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Code unique de la matière (ex: ALGO, BD, WEB)
     * Utilisé pour identifier la matière dans l'emploi du temps
     */
    #[ORM\Column(length: 50, unique: true)]
    private ?string $code = null;

    /**
     * Nom complet de la matière (ex: Algorithmique, Base de données)
     */
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * Collection des séances de cours pour cette matière
     * Relation OneToMany vers CourseSession
     * @var Collection<int, CourseSession>
     */
    #[ORM\OneToMany(targetEntity: CourseSession::class, mappedBy: 'subject')]
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
     * Retourne l'identifiant de la matière
     * @return int|null L'identifiant unique
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne le code de la matière
     * @return string|null Le code (ex: ALGO)
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Définit le code de la matière
     * @param string $code Le nouveau code
     * @return static L'instance courante
     */
    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Retourne le nom de la matière
     * @return string|null Le nom complet
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Définit le nom de la matière
     * @param string $name Le nouveau nom
     * @return static L'instance courante
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Retourne toutes les séances de cours de cette matière
     * @return Collection<int, CourseSession> Collection des séances
     */
    public function getCourseSessions(): Collection
    {
        return $this->courseSessions;
    }

    /**
     * Ajoute une séance de cours à la matière
     * Maintient la relation bidirectionnelle
     * @param CourseSession $courseSession La séance à ajouter
     * @return static L'instance courante
     */
    public function addCourseSession(CourseSession $courseSession): static
    {
        if (!$this->courseSessions->contains($courseSession)) {
            $this->courseSessions->add($courseSession);
            $courseSession->setSubject($this);
        }

        return $this;
    }

    /**
     * Retire une séance de cours de la matière
     * Maintient la relation bidirectionnelle
     * @param CourseSession $courseSession La séance à retirer
     * @return static L'instance courante
     */
    public function removeCourseSession(CourseSession $courseSession): static
    {
        if ($this->courseSessions->removeElement($courseSession)) {
            // Définit le côté owning à null (sauf si déjà changé)
            if ($courseSession->getSubject() === $this) {
                $courseSession->setSubject(null);
            }
        }

        return $this;
    }
}
