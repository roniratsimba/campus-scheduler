<?php

namespace App\Entity;

use App\Repository\LevelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité Level - Niveau académique
 * 
 * Représente un niveau d'études (ex: L1, L2, L3, M1, M2).
 * Les niveaux sont utilisés pour organiser les groupes académiques.
 * 
 * @author Campus Scheduler Team
 * @version 1.0
 */
#[ORM\Entity(repositoryClass: LevelRepository::class)]
class Level
{
    /**
     * Identifiant unique du niveau
     * Généré automatiquement par la base de données
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Code unique du niveau (ex: L1, L2, M1)
     * Correspond aux années d'études (Licence 1, Master 1, etc.)
     */
    #[ORM\Column(length: 10, unique: true)]
    private ?string $code = null;

    /**
     * Collection des groupes académiques de ce niveau
     * Relation OneToMany vers AcademicGroup
     * @var Collection<int, AcademicGroup>
     */
    #[ORM\OneToMany(targetEntity: AcademicGroup::class, mappedBy: 'level')]
    private Collection $academicGroups;

    /**
     * Constructeur - Initialise la collection de groupes académiques
     * Appelé automatiquement lors de l'instanciation
     */
    public function __construct()
    {
        $this->academicGroups = new ArrayCollection();
    }

    /**
     * Retourne l'identifiant du niveau
     * @return int|null L'identifiant unique
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne le code du niveau
     * @return string|null Le code (ex: L1)
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Définit le code du niveau
     * @param string $code Le nouveau code
     * @return static L'instance courante
     */
    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Retourne tous les groupes académiques de ce niveau
     * @return Collection<int, AcademicGroup> Collection des groupes
     */
    public function getAcademicGroups(): Collection
    {
        return $this->academicGroups;
    }

    /**
     * Ajoute un groupe académique au niveau
     * Maintient la relation bidirectionnelle
     * @param AcademicGroup $academicGroup Le groupe à ajouter
     * @return static L'instance courante
     */
    public function addAcademicGroup(AcademicGroup $academicGroup): static
    {
        if (!$this->academicGroups->contains($academicGroup)) {
            $this->academicGroups->add($academicGroup);
            $academicGroup->setLevel($this);
        }

        return $this;
    }

    /**
     * Retire un groupe académique du niveau
     * Maintient la relation bidirectionnelle
     * @param AcademicGroup $academicGroup Le groupe à retirer
     * @return static L'instance courante
     */
    public function removeAcademicGroup(AcademicGroup $academicGroup): static
    {
        if ($this->academicGroups->removeElement($academicGroup)) {
            // Définit le côté owning à null (sauf si déjà changé)
            if ($academicGroup->getLevel() === $this) {
                $academicGroup->setLevel(null);
            }
        }

        return $this;
    }

}
