<?php

namespace App\Entity;

use App\Repository\CourseSessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\DeliveryMode;

#[ORM\Entity(repositoryClass: CourseSessionRepository::class)]
class CourseSession
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'courseSessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Teacher $teacher = null;

    #[ORM\ManyToOne(inversedBy: 'courseSessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Subject $subject = null;

    #[ORM\ManyToOne(inversedBy: 'courseSessions')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Room $room = null;

    #[ORM\ManyToOne(inversedBy: 'courseSessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TimeSlot $timeSlot = null;

    /**
     * @var Collection<int, AcademicGroup>
     */
    #[ORM\ManyToMany(targetEntity: AcademicGroup::class, inversedBy: 'courseSessions')]
    private Collection $academicGroups;

    #[ORM\Column(length: 20)]
    private ?string $status = null;

    #[ORM\Column(enumType: DeliveryMode::class)]
    private ?DeliveryMode $deliveryMode = null;

    #[ORM\ManyToOne(inversedBy: 'courseSessions')]
    private ?ScheduleWeek $scheduleWeek = null;

    public function __construct()
    {
        $this->academicGroups = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): static
    {
        $this->teacher = $teacher;

        return $this;
    }

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): static
    {
        $this->room = $room;

        return $this;
    }

    public function getTimeSlot(): ?TimeSlot
    {
        return $this->timeSlot;
    }

    public function setTimeSlot(?TimeSlot $timeSlot): static
    {
        $this->timeSlot = $timeSlot;

        return $this;
    }

    /**
     * @return Collection<int, AcademicGroup>
     */
    public function getAcademicGroups(): Collection
    {
        return $this->academicGroups;
    }

    public function addAcademicGroup(AcademicGroup $academicGroup): static
    {
        if (!$this->academicGroups->contains($academicGroup)) {
            $this->academicGroups->add($academicGroup);
        }

        return $this;
    }

    public function removeAcademicGroup(AcademicGroup $academicGroup): static
    {
        $this->academicGroups->removeElement($academicGroup);

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDeliveryMode(): ?DeliveryMode
    {
        return $this->deliveryMode;
    }

    public function setDeliveryMode(DeliveryMode $deliveryMode): static
    {
        $this->deliveryMode = $deliveryMode;

        return $this;
    }

    public function getScheduleWeek(): ?ScheduleWeek
    {
        return $this->scheduleWeek;
    }

    public function setScheduleWeek(?ScheduleWeek $scheduleWeek): static
    {
        $this->scheduleWeek = $scheduleWeek;

        return $this;
    }
}
