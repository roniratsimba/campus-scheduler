<?php

namespace App\Controller;

use App\Entity\Subject;
use App\Repository\SubjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur SubjectController - Gestion des matières
 * 
 * Fournit les endpoints CRUD pour les matières enseignées.
 * Ces endpoints nécessitent une authentification admin.
 * 
 * Routes disponibles :
 * - GET /api/subjects : Liste toutes les matières
 * - GET /api/subjects/{id} : Détails d'une matière
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
}
