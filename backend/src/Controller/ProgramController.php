<?php

namespace App\Controller;

use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Program;

/**
 * Contrôleur ProgramController - Gestion des programmes d'études
 * 
 * Fournit les endpoints CRUD pour les programmes (filières).
 * Ces endpoints nécessitent une authentification admin.
 * 
 * Routes disponibles :
 * - GET /api/programs : Liste tous les programmes
 * - GET /api/programs/{id} : Détails d'un programme
 * 
 * @author Campus Scheduler Team
 * @version 1.0
 */
#[Route('/api/programs')]
final class ProgramController extends AbstractController
{
    /**
     * Liste tous les programmes d'études
     * 
     * @param ProgramRepository $programRepository Repository des programmes
     * @return JsonResponse JSON avec la liste des programmes
     */
    #[Route('', methods: ['GET'])]
    public function index(ProgramRepository $programRepository): JsonResponse
    {
        $programs = $programRepository->findAll();

        return $this->json(array_map(
            fn(Program $program) => [
                'id' => $program->getId(),
                'code' => $program->getCode(),
                'name' => $program->getName(),
            ],
            $programs
        ));
    }

    /**
     * Affiche les détails d'un programme d'études
     * 
     * @param int $id Identifiant du programme
     * @param ProgramRepository $programRepository Repository des programmes
     * @return JsonResponse JSON avec les détails du programme ou 404
     */
    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, ProgramRepository $programRepository): JsonResponse
    {
        $program = $programRepository->find($id);

        if (!$program) {
            return $this->json(
                ['message' => 'Program not found'],
                404
            );
        }

        return $this->json([
            'id' => $program->getId(),
            'code' => $program->getCode(),
            'name' => $program->getName(),
        ]);
    }
}
