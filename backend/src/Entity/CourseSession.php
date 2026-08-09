<?php

namespace App\Entity;

use App\Repository\CourseSessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\DeliveryMode;

/**
 * Entité CourseSession - Séance de cours
 * 
 * Représente une séance de cours planifiée avec un enseignant,
 * une matière, une salle, un créneau horaire et des groupes.
 * C'est l'entité centrale de l'emploi du temps.
 * 
 * @author Campus Scheduler Team
 * @version 1.0
 */
#[ORM\Entity(repositoryClass: CourseSessionRepository::class)]
class CourseSession
{
    /**
     * Identifiant unique de la séance
     * Généré automatiquement par la base de données
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Enseignant responsable de la séance
     * Relation ManyToOne vers l'entité Teacher
     */
    #[ORM\ManyToOne(inversedBy: 'courseSessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Teacher $teacher = null;

    /**
     * Matière enseignée lors de la séance
     * Relation ManyToOne vers l'entité Subject
     */
    #[ORM\ManyToOne(inversedBy: 'courseSessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Subject $subject = null;

    /**
     * Salle où se déroule la séance (optionnel)
     * Relation ManyToOne vers l'entité Room
     */
    #[ORM\ManyToOne(inversedBy: 'courseSessions')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Room $room = null;

    /**
     * Créneau horaire de la séance
     * Relation ManyToOne vers l'entité TimeSlot
     */
    #[ORM\ManyToOne(inversedBy: 'courseSessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TimeSlot $timeSlot = null;

    /**
     * Collection des groupes académiques participant à la séance
     * Relation ManyToMany avec AcademicGroup
     * @var Collection<int, AcademicGroup>
     */
    #[ORM\ManyToMany(targetEntity: AcademicGroup::class, inversedBy: 'courseSessions')]
    private Collection $academicGroups;

    /**
     * Statut de la séance (ex: scheduled, cancelled, completed)
     */
    #[ORM\Column(length: 20)]
    private ?string $status = null;

    /**
     * Mode de livraison de la séance (présentiel, distanciel, hybride)
     * Enuméré via DeliveryMode
     */
    #[ORM\Column(enumType: DeliveryMode::class)]
    private ?DeliveryMode $deliveryMode = null;

    /**
     * Semaine d'emploi du temps de cette séance
     * Relation ManyToOne vers l'entité ScheduleWeek
     */
    #[ORM\ManyToOne(inversedBy: 'courseSessions')]
    private ?ScheduleWeek $scheduleWeek = null;

    /**
     * Constructeur - Initialise la collection de groupes
     * Appelé automatiquement lors de l'instanciation
     */
    public function __construct()
    {
        $this->academicGroups = new ArrayCollection();
    }

    /**
     * Retourne l'identifiant de la séance
     * @return int|null L'identifiant unique
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne l'enseignant de la séance
     * @return Teacher|null L'enseignant
     */
    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }

    /**
     * Définit l'enseignant de la séance
     * @param Teacher|null $teacher Le nouvel enseignant
     * @return static L'instance courante
     */
    public function setTeacher(?Teacher $teacher): static
    {
        $this->teacher = $teacher;

        return $this;
    }

    /**
     * Retourne la matière de la séance
     * @return Subject|null La matière
     */
    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    /**
     * Définit la matière de la séance
     * @param Subject|null $subject La nouvelle matière
     * @return static L'instance courante
     */
    public function setSubject(?Subject $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Retourne la salle de la séance
     * @return Room|null La salle (peut être null)
     */
    public function getRoom(): ?Room
    {
        return $this->room;
    }

    /**
     * Définit la salle de la séance
     * @param Room|null $room La nouvelle salle ou null
     * @return static L'instance courante
     */
    public function setRoom(?Room $room): static
    {
        $this->room = $room;

        return $this;
    }

    /**
     * Retourne le créneau horaire de la séance
     * @return TimeSlot|null Le créneau horaire
     */
    public function getTimeSlot(): ?TimeSlot
    {
        return $this->timeSlot;
    }

    /**
     * Définit le créneau horaire de la séance
     * @param TimeSlot|null $timeSlot Le nouveau créneau
     * @return static L'instance courante
     */
    public function setTimeSlot(?TimeSlot $timeSlot): static
    {
        $this->timeSlot = $timeSlot;

        return $this;
    }

    /**
     * Retourne tous les groupes participant à la séance
     * @return Collection<int, AcademicGroup> Collection des groupes
     */
    public function getAcademicGroups(): Collection
    {
        return $this->academicGroups;
    }

    /**
     * Ajoute un groupe à la séance
     * @param AcademicGroup $academicGroup Le groupe à ajouter
     * @return static L'instance courante
     */
    public function addAcademicGroup(AcademicGroup $academicGroup): static
    {
        if (!$this->academicGroups->contains($academicGroup)) {
            $this->academicGroups->add($academicGroup);
        }

        return $this;
    }

    /**
     * Retire un groupe de la séance
     * @param AcademicGroup $academicGroup Le groupe à retirer
     * @return static L'instance courante
     */
    public function removeAcademicGroup(AcademicGroup $academicGroup): static
    {
        $this->academicGroups->removeElement($academicGroup);

        return $this;
    }

    /**
     * Retourne le statut de la séance
     * @return string|null Le statut
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Définit le statut de la séance
     * @param string $status Le nouveau statut
     * @return static L'instance courante
     */
    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Retourne le mode de livraison de la séance
     * @return DeliveryMode|null Le mode (présentiel, distanciel, hybride)
     */
    public function getDeliveryMode(): ?DeliveryMode
    {
        return $this->deliveryMode;
    }

    /**
     * Définit le mode de livraison de la séance
     * @param DeliveryMode $deliveryMode Le nouveau mode
     * @return static L'instance courante
     */
    public function setDeliveryMode(DeliveryMode $deliveryMode): static
    {
        $this->deliveryMode = $deliveryMode;

        return $this;
    }

    /**
     * Retourne la semaine d'emploi du temps
     * @return ScheduleWeek|null La semaine
     */
    public function getScheduleWeek(): ?ScheduleWeek
    {
        return $this->scheduleWeek;
    }

    /**
     * Définit la semaine d'emploi du temps
     * @param ScheduleWeek|null $scheduleWeek La nouvelle semaine
     * @return static L'instance courante
     */
    public function setScheduleWeek(?ScheduleWeek $scheduleWeek): static
    {
        $this->scheduleWeek = $scheduleWeek;

        return $this;
    }
}
