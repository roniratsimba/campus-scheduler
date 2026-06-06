<?php

namespace App\Entity;

use App\Repository\ProgramRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProgramRepository::class)]
class Program
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, AcademicGroup>
     */
    #[ORM\OneToMany(targetEntity: AcademicGroup::class, mappedBy: 'program')]
    private Collection $academicGroups;

    public function __construct()
    {
        $this->academicGroups = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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
            $academicGroup->setProgram($this);
        }

        return $this;
    }

    public function removeAcademicGroup(AcademicGroup $academicGroup): static
    {
        if ($this->academicGroups->removeElement($academicGroup)) {
            // set the owning side to null (unless already changed)
            if ($academicGroup->getProgram() === $this) {
                $academicGroup->setProgram(null);
            }
        }

        return $this;
    }
}
