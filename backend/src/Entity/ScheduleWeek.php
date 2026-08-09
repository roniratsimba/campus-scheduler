<?php

namespace App\Entity;

use App\Repository\ScheduleWeekRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité ScheduleWeek - Semaine d'emploi du temps
 * 
 * Représente une semaine calendaire avec un statut de publication.
 * Les séances de cours sont rattachées à une semaine spécifique.
 * 
 * @author Campus Scheduler Team
 * @version 1.0
 */
#[ORM\Entity(repositoryClass: ScheduleWeekRepository::class)]
class ScheduleWeek
{
    /**
     * Identifiant unique de la semaine
     * Généré automatiquement par la base de données
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Date de début de la semaine (unique)
     * Utilisée comme identifiant naturel de la semaine
     */
    #[ORM\Column(type: Types::DATE_IMMUTABLE, unique: true)]
    private ?\DateTimeImmutable $startDate = null;

    /**
     * Date de fin de la semaine
     */
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $endDate = null;

    /**
     * Statut de la semaine (ex: draft, published)
     * Indique si l'emploi du temps est visible par les étudiants
     */
    #[ORM\Column(length: 20)]
    private ?string $status = null;

    /**
     * Date et heure de publication de la semaine
     * Null si la semaine n'est pas encore publiée
     */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    /**
     * Collection des séances de cours de cette semaine
     * Relation OneToMany vers CourseSession
     * @var Collection<int, CourseSession>
     */
    #[ORM\OneToMany(targetEntity: CourseSession::class, mappedBy: 'scheduleWeek')]
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
     * Retourne l'identifiant de la semaine
     * @return int|null L'identifiant unique
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne la date de début de la semaine
     * @return \DateTimeImmutable|null La date de début
     */
    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    /**
     * Définit la date de début de la semaine
     * @param \DateTimeImmutable $startDate La nouvelle date de début
     * @return static L'instance courante
     */
    public function setStartDate(\DateTimeImmutable $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Retourne la date de fin de la semaine
     * @return \DateTimeImmutable|null La date de fin
     */
    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    /**
     * Définit la date de fin de la semaine
     * @param \DateTimeImmutable $endDate La nouvelle date de fin
     * @return static L'instance courante
     */
    public function setEndDate(\DateTimeImmutable $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Retourne le statut de la semaine
     * @return string|null Le statut (draft, published)
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Définit le statut de la semaine
     * @param string $status Le nouveau statut
     * @return static L'instance courante
     */
    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Retourne la date de publication
     * @return \DateTimeImmutable|null La date de publication ou null
     */
    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    /**
     * Définit la date de publication
     * @param \DateTimeImmutable|null $publishedAt La nouvelle date ou null
     * @return static L'instance courante
     */
    public function setPublishedAt(?\DateTimeImmutable $publishedAt): static
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * Retourne toutes les séances de cours de cette semaine
     * @return Collection<int, CourseSession> Collection des séances
     */
    public function getCourseSessions(): Collection
    {
        return $this->courseSessions;
    }

    /**
     * Ajoute une séance de cours à la semaine
     * Maintient la relation bidirectionnelle
     * @param CourseSession $courseSession La séance à ajouter
     * @return static L'instance courante
     */
    public function addCourseSession(CourseSession $courseSession): static
    {
        if (!$this->courseSessions->contains($courseSession)) {
            $this->courseSessions->add($courseSession);
            $courseSession->setScheduleWeek($this);
        }

        return $this;
    }

    /**
     * Retire une séance de cours de la semaine
     * Maintient la relation bidirectionnelle
     * @param CourseSession $courseSession La séance à retirer
     * @return static L'instance courante
     */
    public function removeCourseSession(CourseSession $courseSession): static
    {
        if ($this->courseSessions->removeElement($courseSession)) {
            // Définit le côté owning à null (sauf si déjà changé)
            if ($courseSession->getScheduleWeek() === $this) {
                $courseSession->setScheduleWeek(null);
            }
        }

        return $this;
    }
}
