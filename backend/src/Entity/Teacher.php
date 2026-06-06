<?php

namespace App\Entity;

use App\Repository\TeacherRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeacherRepository::class)]
class Teacher
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $firstName = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, unique:true)]
    private ?string $email = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    /**
     * @var Collection<int, CourseSession>
     */
    #[ORM\OneToMany(targetEntity: CourseSession::class, mappedBy: 'teacher')]
    private Collection $courseSessions;

    public function __construct()
    {
        $this->courseSessions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

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
            $courseSession->setTeacher($this);
        }

        return $this;
    }

    public function removeCourseSession(CourseSession $courseSession): static
    {
        if ($this->courseSessions->removeElement($courseSession)) {
            // set the owning side to null (unless already changed)
            if ($courseSession->getTeacher() === $this) {
                $courseSession->setTeacher(null);
            }
        }

        return $this;
    }
}
