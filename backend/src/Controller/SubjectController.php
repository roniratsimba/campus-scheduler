<?php

namespace App\Controller;

use App\Entity\Subject;
use App\Repository\SubjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contrôleur SubjectController - Gestion des matières
 * 
 * Fournit les endpoints CRUD pour les matières enseignées.
 * Ces endpoints nécessitent une authentification admin.
 * 
 * Routes disponibles :
 * - GET /api/subjects : Liste toutes les matières
 * - GET /api/subjects/{id} : Détails d'une matière
 * - POST /api/subjects : Crée une matière
 * - PUT /api/subjects/{id} : Modifie une matière
 * - DELETE /api/subjects/{id} : Supprime une matière
 * 
 * @author Campus Scheduler Team
 * @version 1.0
 */
#[Route('/api/subjects')]
final class SubjectController extends AbstractController
{
    /**
     * Liste toutes les matières
     * 
     * @param SubjectRepository $subjectRepository Repository des matières
     * @return JsonResponse JSON avec la liste des matières
     */
    #[Route('', methods: ['GET'])]
    public function index(SubjectRepository $subjectRepository): JsonResponse
    {
        $subjects = $subjectRepository->findAll();

        return $this->json(array_map(
            fn(Subject $subject) => [
                'id' => $subject->getId(),
                'code' => $subject->getCode(),
                'name' => $subject->getName(),
            ],
            $subjects
        ));
    }

    /**
     * Affiche les détails d'une matière
     * 
     * @param int $id Identifiant de la matière
     * @param SubjectRepository $subjectRepository Repository des matières
     * @return JsonResponse JSON avec les détails de la matière ou 404
     */
    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, SubjectRepository $subjectRepository): JsonResponse
    {
        $subject = $subjectRepository->find($id);

        if (!$subject) {
            return $this->json(
                ['message' => 'Subject not found'],
                404
            );
        }

        return $this->json([
            'id' => $subject->getId(),
            'code' => $subject->getCode(),
            'name' => $subject->getName(),
        ]);
    }

    /**
     * Crée une nouvelle matière
     * 
     * @param Request $request Requête HTTP avec les données de la matière
     * @param EntityManagerInterface $em EntityManager Doctrine
     * @return JsonResponse JSON avec la matière créée
     */
    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $subject = new Subject();
        $subject->setCode($data['code'] ?? '');
        $subject->setName($data['name'] ?? '');

        $em->persist($subject);
        $em->flush();

        return $this->json([
            'id' => $subject->getId(),
            'code' => $subject->getCode(),
            'name' => $subject->getName(),
        ], 201);
    }

    /**
     * Modifie une matière existante
     * 
     * @param int $id Identifiant de la matière
     * @param Request $request Requête HTTP avec les données modifiées
     * @param SubjectRepository $subjectRepository Repository des matières
     * @param EntityManagerInterface $em EntityManager Doctrine
     * @return JsonResponse JSON avec la matière modifiée ou 404
     */
    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request, SubjectRepository $subjectRepository, EntityManagerInterface $em): JsonResponse
    {
        $subject = $subjectRepository->find($id);

        if (!$subject) {
            return $this->json(['message' => 'Subject not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $subject->setCode($data['code'] ?? $subject->getCode());
        $subject->setName($data['name'] ?? $subject->getName());

        $em->flush();

        return $this->json([
            'id' => $subject->getId(),
            'code' => $subject->getCode(),
            'name' => $subject->getName(),
        ]);
    }

    /**
     * Supprime une matière
     * 
     * @param int $id Identifiant de la matière
     * @param SubjectRepository $subjectRepository Repository des matières
     * @param EntityManagerInterface $em EntityManager Doctrine
     * @return JsonResponse JSON de confirmation ou 404
     */
    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, SubjectRepository $subjectRepository, EntityManagerInterface $em): JsonResponse
    {
        $subject = $subjectRepository->find($id);

        if (!$subject) {
            return $this->json(['message' => 'Subject not found'], 404);
        }

        $em->remove($subject);
        $em->flush();

        return $this->json(['message' => 'Subject deleted successfully']);
    }
}
