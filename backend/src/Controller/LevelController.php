<?php

namespace App\Controller;

use App\Repository\LevelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Level;

/**
 * Contrôleur LevelController - Gestion des niveaux académiques
 * 
 * Fournit les endpoints CRUD pour les niveaux (L1, L2, M1, etc.).
 * Ces endpoints nécessitent une authentification admin.
 * 
 * Routes disponibles :
 * - GET /api/levels : Liste tous les niveaux
 * - GET /api/levels/{id} : Détails d'un niveau
 * 
 * @author Campus Scheduler Team
 * @version 1.0
 */
#[Route('/api/levels')]
final class LevelController extends AbstractController
{
    /**
     * Liste tous les niveaux académiques
     * 
     * @param LevelRepository $levelRepository Repository des niveaux
     * @return JsonResponse JSON avec la liste des niveaux
     */
    #[Route('', methods: ['GET'])]
    public function index(LevelRepository $levelRepository): JsonResponse
    {
        $levels = $levelRepository->findAll();

        return $this->json(array_map(
            fn(Level $level) => [
                'id' => $level->getId(),
                'code' => $level->getCode(),
            ],
            $levels
        ));
    }

    /**
     * Affiche les détails d'un niveau académique
     * 
     * @param int $id Identifiant du niveau
     * @param LevelRepository $levelRepository Repository des niveaux
     * @return JsonResponse JSON avec les détails du niveau ou 404
     */
    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, LevelRepository $levelRepository): JsonResponse
    {
        $level = $levelRepository->find($id);

        if (!$level) {
            return $this->json(
                ['message' => 'Level not found'],
                404
            );
        }

        return $this->json([
            'id' => $level->getId(),
            'code' => $level->getCode(),
        ]);
    }
}