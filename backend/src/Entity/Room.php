<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité Room - Salle de cours
 * 
 * Représente une salle physique où se déroulent les cours.
 * Une salle peut être de différents types (classe, laboratoire, amphithéâtre).
 * 
 * @author Campus Scheduler Team
 * @version 1.0
 */
#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
{
    /**
     * Identifiant unique de la salle
     * Généré automatiquement par la base de données
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Code unique de la salle (ex: A101, B201)
     * Utilisé pour identifier la salle dans l'emploi du temps
     */
    #[ORM\Column(length: 50, unique: true)]
    private ?string $code = null;

    /**
     * Nom descriptif de la salle (ex: Salle de cours A101)
     */
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * Type de salle (ex: classroom, lab, amphitheater)
     * Permet de catégoriser les salles selon leur usage
     */
    #[ORM\Column(length: 50)]
    private ?string $type = null;

    /**
     * Collection des séances de cours se déroulant dans cette salle
     * Relation OneToMany vers CourseSession
     * @var Collection<int, CourseSession>
     */
    #[ORM\OneToMany(targetEntity: CourseSession::class, mappedBy: 'room')]
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
     * Retourne l'identifiant de la salle
     * @return int|null L'identifiant unique
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne le code de la salle
     * @return string|null Le code (ex: A101)
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Définit le code de la salle
     * @param string $code Le nouveau code
     * @return static L'instance courante
     */
    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Retourne le nom de la salle
     * @return string|null Le nom descriptif
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Définit le nom de la salle
     * @param string $name Le nouveau nom
     * @return static L'instance courante
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Retourne le type de la salle
     * @return string|null Le type (classroom, lab, etc.)
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Définit le type de la salle
     * @param string $type Le nouveau type
     * @return static L'instance courante
     */
    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Retourne toutes les séances de cours dans cette salle
     * @return Collection<int, CourseSession> Collection des séances
     */
    public function getCourseSessions(): Collection
    {
        return $this->courseSessions;
    }

    /**
     * Ajoute une séance de cours à la salle
     * Maintient la relation bidirectionnelle
     * @param CourseSession $courseSession La séance à ajouter
     * @return static L'instance courante
     */
    public function addCourseSession(CourseSession $courseSession): static
    {
        if (!$this->courseSessions->contains($courseSession)) {
            $this->courseSessions->add($courseSession);
            $courseSession->setRoom($this);
        }

        return $this;
    }

    /**
     * Retire une séance de cours de la salle
     * Maintient la relation bidirectionnelle
     * @param CourseSession $courseSession La séance à retirer
     * @return static L'instance courante
     */
    public function removeCourseSession(CourseSession $courseSession): static
    {
        if ($this->courseSessions->removeElement($courseSession)) {
            // Définit le côté owning à null (sauf si déjà changé)
            if ($courseSession->getRoom() === $this) {
                $courseSession->setRoom(null);
            }
        }

        return $this;
    }
}
