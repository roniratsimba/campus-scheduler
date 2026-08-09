<?php

namespace App\Entity;

use App\Repository\TimeSlotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité TimeSlot - Créneau horaire
 * 
 * Représente un créneau horaire dans la semaine (ex: Lundi 08:00-10:00).
 * Les créneaux horaires sont réutilisables d'une semaine à l'autre.
 * 
 * @author Campus Scheduler Team
 * @version 1.0
 */
#[ORM\Entity(repositoryClass: TimeSlotRepository::class)]
class TimeSlot
{
    /**
     * Identifiant unique du créneau horaire
     * Généré automatiquement par la base de données
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Jour de la semaine (ex: MONDAY, TUESDAY)
     * Utilise les constantes de l'enum DayOfWeek
     */
    #[ORM\Column(length: 20)]
    private ?string $dayOfWeek = null;

    /**
     * Heure de début du créneau
     * Stockée comme objet DateTime
     */
    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $startTime = null;

    /**
     * Heure de fin du créneau
     * Stockée comme objet DateTime
     */
    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $endTime = null;

    /**
     * Collection des séances de cours programmées sur ce créneau
     * Relation OneToMany vers CourseSession
     * @var Collection<int, CourseSession>
     */
    #[ORM\OneToMany(targetEntity: CourseSession::class, mappedBy: 'timeSlot')]
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
     * Retourne l'identifiant du créneau horaire
     * @return int|null L'identifiant unique
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne le jour de la semaine
     * @return string|null Le jour (ex: MONDAY)
     */
    public function getDayOfWeek(): ?string
    {
        return $this->dayOfWeek;
    }

    /**
     * Définit le jour de la semaine
     * @param string $dayOfWeek Le nouveau jour
     * @return static L'instance courante
     */
    public function setDayOfWeek(string $dayOfWeek): static
    {
        $this->dayOfWeek = $dayOfWeek;

        return $this;
    }

    /**
     * Retourne l'heure de début
     * @return \DateTime|null L'heure de début
     */
    public function getStartTime(): ?\DateTime
    {
        return $this->startTime;
    }

    /**
     * Définit l'heure de début
     * @param \DateTime $startTime La nouvelle heure de début
     * @return static L'instance courante
     */
    public function setStartTime(\DateTime $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Retourne l'heure de fin
     * @return \DateTime|null L'heure de fin
     */
    public function getEndTime(): ?\DateTime
    {
        return $this->endTime;
    }

    /**
     * Définit l'heure de fin
     * @param \DateTime $endTime La nouvelle heure de fin
     * @return static L'instance courante
     */
    public function setEndTime(\DateTime $endTime): static
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Retourne toutes les séances de cours sur ce créneau
     * @return Collection<int, CourseSession> Collection des séances
     */
    public function getCourseSessions(): Collection
    {
        return $this->courseSessions;
    }

    /**
     * Ajoute une séance de cours au créneau
     * Maintient la relation bidirectionnelle
     * @param CourseSession $courseSession La séance à ajouter
     * @return static L'instance courante
     */
    public function addCourseSession(CourseSession $courseSession): static
    {
        if (!$this->courseSessions->contains($courseSession)) {
            $this->courseSessions->add($courseSession);
            $courseSession->setTimeSlot($this);
        }

        return $this;
    }

    /**
     * Retire une séance de cours du créneau
     * Maintient la relation bidirectionnelle
     * @param CourseSession $courseSession La séance à retirer
     * @return static L'instance courante
     */
    public function removeCourseSession(CourseSession $courseSession): static
    {
        if ($this->courseSessions->removeElement($courseSession)) {
            // Définit le côté owning à null (sauf si déjà changé)
            if ($courseSession->getTimeSlot() === $this) {
                $courseSession->setTimeSlot(null);
            }
        }

        return $this;
    }
}
