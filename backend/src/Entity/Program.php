<?php

namespace App\Entity;

use App\Repository\ProgramRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité Program - Programme d'études
 * 
 * Représente un programme ou filière d'études (ex: Génie Logiciel, Systèmes et Réseaux).
 * Les programmes regroupent les étudiants par spécialisation.
 * 
 * @author Campus Scheduler Team
 * @version 1.0
 */
#[ORM\Entity(repositoryClass: ProgramRepository::class)]
class Program
{
    /**
     * Identifiant unique du programme
     * Généré automatiquement par la base de données
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Code unique du programme (ex: GB, SR, IA)
     * Abréviation utilisée dans l'emploi du temps
     */
    #[ORM\Column(length: 50, unique: true)]
    private ?string $code = null;

    /**
     * Nom complet du programme (ex: Génie Logiciel, Systèmes et Réseaux)
     */
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * Collection des groupes académiques de ce programme
     * Relation OneToMany vers AcademicGroup
     * @var Collection<int, AcademicGroup>
     */
    #[ORM\OneToMany(targetEntity: AcademicGroup::class, mappedBy: 'program')]
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
     * Retourne l'identifiant du programme
     * @return int|null L'identifiant unique
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne le code du programme
     * @return string|null Le code (ex: GB)
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Définit le code du programme
     * @param string $code Le nouveau code
     * @return static L'instance courante
     */
    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Retourne le nom du programme
     * @return string|null Le nom complet
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Définit le nom du programme
     * @param string $name Le nouveau nom
     * @return static L'instance courante
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Retourne tous les groupes académiques de ce programme
     * @return Collection<int, AcademicGroup> Collection des groupes
     */
    public function getAcademicGroups(): Collection
    {
        return $this->academicGroups;
    }

    /**
     * Ajoute un groupe académique au programme
     * Maintient la relation bidirectionnelle
     * @param AcademicGroup $academicGroup Le groupe à ajouter
     * @return static L'instance courante
     */
    public function addAcademicGroup(AcademicGroup $academicGroup): static
    {
        if (!$this->academicGroups->contains($academicGroup)) {
            $this->academicGroups->add($academicGroup);
            $academicGroup->setProgram($this);
        }

        return $this;
    }

    /**
     * Retire un groupe académique du programme
     * Maintient la relation bidirectionnelle
     * @param AcademicGroup $academicGroup Le groupe à retirer
     * @return static L'instance courante
     */
    public function removeAcademicGroup(AcademicGroup $academicGroup): static
    {
        if ($this->academicGroups->removeElement($academicGroup)) {
            // Définit le côté owning à null (sauf si déjà changé)
            if ($academicGroup->getProgram() === $this) {
                $academicGroup->setProgram(null);
            }
        }

        return $this;
    }
}
