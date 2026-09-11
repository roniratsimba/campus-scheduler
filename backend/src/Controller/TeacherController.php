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
 * - POST /api/teachers : Crée un enseignant
 * - PUT /api/teachers/{id} : Modifie un enseignant
 * - DELETE /api/teachers/{id} : Supprime un enseignant
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

    /**
     * Crée un nouvel enseignant
     * 
     * @param Request $request Requête HTTP avec les données de l'enseignant
     * @param EntityManagerInterface $em EntityManager Doctrine
     * @return JsonResponse JSON avec l'enseignant créé
     */
    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $teacher = new Teacher();
        $teacher->setFirstName($data['firstName'] ?? '');
        $teacher->setLastName($data['lastName'] ?? null);
        $teacher->setEmail($data['email'] ?? '');
        $teacher->setIsActive($data['active'] ?? true);

        $em->persist($teacher);
        $em->flush();

        return $this->json([
            'id' => $teacher->getId(),
            'firstName' => $teacher->getFirstName(),
            'lastName' => $teacher->getLastName(),
            'email' => $teacher->getEmail(),
            'active' => $teacher->isActive(),
        ], 201);
    }

    /**
     * Modifie un enseignant existant
     * 
     * @param int $id Identifiant de l'enseignant
     * @param Request $request Requête HTTP avec les données modifiées
     * @param TeacherRepository $teacherRepository Repository des enseignants
     * @param EntityManagerInterface $em EntityManager Doctrine
     * @return JsonResponse JSON avec l'enseignant modifié ou 404
     */
    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request, TeacherRepository $teacherRepository, EntityManagerInterface $em): JsonResponse
    {
        $teacher = $teacherRepository->find($id);

        if (!$teacher) {
            return $this->json(['message' => 'Teacher not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $teacher->setFirstName($data['firstName'] ?? $teacher->getFirstName());
        $teacher->setLastName($data['lastName'] ?? $teacher->getLastName());
        $teacher->setEmail($data['email'] ?? $teacher->getEmail());
        $teacher->setIsActive($data['active'] ?? $teacher->isActive());

        $em->flush();

        return $this->json([
            'id' => $teacher->getId(),
            'firstName' => $teacher->getFirstName(),
            'lastName' => $teacher->getLastName(),
            'email' => $teacher->getEmail(),
            'active' => $teacher->isActive(),
        ]);
    }

    /**
     * Supprime un enseignant
     * 
     * @param int $id Identifiant de l'enseignant
     * @param TeacherRepository $teacherRepository Repository des enseignants
     * @param EntityManagerInterface $em EntityManager Doctrine
     * @return JsonResponse JSON de confirmation ou 404
     */
    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, TeacherRepository $teacherRepository, EntityManagerInterface $em): JsonResponse
    {
        $teacher = $teacherRepository->find($id);

        if (!$teacher) {
            return $this->json(['message' => 'Teacher not found'], 404);
        }

        $em->remove($teacher);
        $em->flush();

        return $this->json(['message' => 'Teacher deleted successfully']);
    }
}