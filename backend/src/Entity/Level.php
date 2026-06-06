<?php

namespace App\Entity;

use App\Repository\LevelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LevelRepository::class)]
class Level
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10, unique: true)]
    private ?string $code = null;

    /**
     * @var Collection<int, AcademicGroup>
     */
    #[ORM\OneToMany(targetEntity: AcademicGroup::class, mappedBy: 'level')]
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
            $academicGroup->setLevel($this);
        }

        return $this;
    }

    public function removeAcademicGroup(AcademicGroup $academicGroup): static
    {
        if ($this->academicGroups->removeElement($academicGroup)) {
            // set the owning side to null (unless already changed)
            if ($academicGroup->getLevel() === $this) {
                $academicGroup->setLevel(null);
            }
        }

        return $this;
    }

}
