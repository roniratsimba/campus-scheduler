<?php

namespace App\Entity;

use App\Repository\AcademicGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $groupNumber = null;

    #[ORM\ManyToOne(inversedBy: 'academicGroups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Level $level = null;

    #[ORM\ManyToOne(inversedBy: 'academicGroups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Program $program = null;

    /**
     * @var Collection<int, CourseSession>
     */
    #[ORM\ManyToMany(targetEntity: CourseSession::class, mappedBy: 'academicGroups')]
    private Collection $courseSessions;

    public function __construct()
    {
        $this->courseSessions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroupNumber(): ?int
    {
        return $this->groupNumber;
    }

    public function setGroupNumber(int $groupNumber): static
    {
        $this->groupNumber = $groupNumber;

        return $this;
    }

    public function getLevel(): ?Level
    {
        return $this->level;
    }

    public function setLevel(?Level $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getProgram(): ?Program
    {
        return $this->program;
    }

    public function setProgram(?Program $program): static
    {
        $this->program = $program;

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
            $courseSession->addAcademicGroup($this);
        }

        return $this;
    }

    public function removeCourseSession(CourseSession $courseSession): static
    {
        if ($this->courseSessions->removeElement($courseSession)) {
            $courseSession->removeAcademicGroup($this);
        }

        return $this;
    }
}
