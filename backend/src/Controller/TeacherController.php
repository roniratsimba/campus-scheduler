<?php

namespace App\Controller;

use App\Repository\TeacherRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Teacher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contrôleur TeacherController - Gestion des enseignants
 * 
 * Fournit les endpoints CRUD pour les enseignants.
 * Ces endpoints nécessitent une authentification admin.
 * 
 * Routes disponibles :
 * - GET /api/teachers : Liste tous les enseignants
 * - GET /api/teachers/{id} : Détails d'un enseignant
 * 
 * @author Campus Scheduler Team
 * @version 1.0
 */
#[Route('/api/teachers')]
final class TeacherController extends AbstractController
{
    /**
     * Liste tous les enseignants
     * 
     * @param TeacherRepository $teacherRepository Repository des enseignants
     * @return JsonResponse JSON avec la liste des enseignants
     */
    #[Route('', methods: ['GET'])]
    public function index(TeacherRepository $teacherRepository): JsonResponse
    {
        $teachers = $teacherRepository->findAll();

        return $this->json(array_map(
            fn(Teacher $teacher) => [
                'id' => $teacher->getId(),
                'firstName' => $teacher->getFirstName(),
                'lastName' => $teacher->getLastName(),
                'email' => $teacher->getEmail(),
                'active' => $teacher->isActive(),
            ],
            $teachers
        ));
    }

    /**
     * Affiche les détails d'un enseignant
     * 
     * @param int $id Identifiant de l'enseignant
     * @param TeacherRepository $teacherRepository Repository des enseignants
     * @return JsonResponse JSON avec les détails de l'enseignant ou 404
     */
    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, TeacherRepository $teacherRepository): JsonResponse
    {
        $teacher = $teacherRepository->find($id);

        if (!$teacher) {
            return $this->json(
                ['message' => 'Teacher not found'],
                404
            );
        }

        return $this->json([
            'id' => $teacher->getId(),
            'firstName' => $teacher->getFirstName(),
            'lastName' => $teacher->getLastName(),
            'email' => $teacher->getEmail(),
            'active' => $teacher->isActive(),
        ]);
    }
}