<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AcademicGroupRepository;
use App\Entity\AcademicGroup;

/**
 * Contrôleur AcademicGroupController - Gestion des groupes académiques
 * 
 * Fournit les endpoints CRUD pour les groupes académiques.
 * Ces endpoints nécessitent une authentification admin.
 * 
 * Routes disponibles :
 * - GET /api/academic-groups : Liste tous les groupes
 * - GET /api/academic-groups/{id} : Détails d'un groupe
 * 
 * @author Campus Scheduler Team
 * @version 1.0
 */
#[Route('/api/academic-groups')]
final class AcademicGroupController extends AbstractController
{
    /**
     * Liste tous les groupes académiques
     * 
     * @param AcademicGroupRepository $academicGroupRepository Repository des groupes
     * @return JsonResponse JSON avec la liste des groupes
     */
    #[Route('', methods: ['GET'])]
    public function index(AcademicGroupRepository $academicGroupRepository): JsonResponse
    {
        $groups = $academicGroupRepository->findAll();

        return $this->json(array_map(
            fn(AcademicGroup $group) => [
                'id' => $group->getId(),
                'groupNumber' => $group->getGroupNumber(),
                'level' => $group->getLevel()?->getCode(),
                'program' => $group->getProgram()?->getCode(),
            ],
            $groups
        ));
    }

    /**
     * Affiche les détails d'un groupe académique
     * 
     * @param int $id Identifiant du groupe
     * @param AcademicGroupRepository $academicGroupRepository Repository des groupes
     * @return JsonResponse JSON avec les détails du groupe ou 404
     */
    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, AcademicGroupRepository $academicGroupRepository): JsonResponse
    {
        $group = $academicGroupRepository->find($id);

        if (!$group) {
            return $this->json(
                ['message' => 'AcademicGroup not found'],
                404
            );
        }

        return $this->json([
            'id' => $group->getId(),
            'groupNumber' => $group->getGroupNumber(),
            'level' => $group->getLevel()?->getCode(),
            'program' => $group->getProgram()?->getCode(),
        ]);
    }
}
