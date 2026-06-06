<?php

namespace App\Entity;

use App\Repository\TimeSlotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TimeSlotRepository::class)]
class TimeSlot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $dayOfWeek = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $startTime = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $endTime = null;

    /**
     * @var Collection<int, CourseSession>
     */
    #[ORM\OneToMany(targetEntity: CourseSession::class, mappedBy: 'timeSlot')]
    private Collection $courseSessions;

    public function __construct()
    {
        $this->courseSessions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDayOfWeek(): ?string
    {
        return $this->dayOfWeek;
    }

    public function setDayOfWeek(string $dayOfWeek): static
    {
        $this->dayOfWeek = $dayOfWeek;

        return $this;
    }

    public function getStartTime(): ?\DateTime
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTime $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTime
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTime $endTime): static
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * @return Collection<int, CourseSession>
     */
    public function getCourseSessions(): Collection
    {
        return $this->courseSessions;
    }

    public function addCourseSession(CourseSession $courseSession): static
    {
        if (!$this->courseSessions->contains($courseSession)) {
            $this->courseSessions->add($courseSession);
            $courseSession->setTimeSlot($this);
        }

        return $this;
    }

    public function removeCourseSession(CourseSession $courseSession): static
    {
        if ($this->courseSessions->removeElement($courseSession)) {
            // set the owning side to null (unless already changed)
            if ($courseSession->getTimeSlot() === $this) {
                $courseSession->setTimeSlot(null);
            }
        }

        return $this;
    }
}
