<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AcademicGroupRepository;
use App\Entity\AcademicGroup;
use App\Repository\LevelRepository;
use App\Repository\ProgramRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contrôleur AcademicGroupController - Gestion des groupes académiques
 * 
 * Fournit les endpoints CRUD pour les groupes académiques.
 * Ces endpoints nécessitent une authentification admin.
 * 
 * Routes disponibles :
 * - GET /api/academic-groups : Liste tous les groupes
 * - GET /api/academic-groups/{id} : Détails d'un groupe
 * - POST /api/academic-groups : Crée un groupe
 * - PUT /api/academic-groups/{id} : Modifie un groupe
 * - DELETE /api/academic-groups/{id} : Supprime un groupe
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

    /**
     * Crée un nouveau groupe académique
     * 
     * @param Request $request Requête HTTP avec les données du groupe
     * @param AcademicGroupRepository $academicGroupRepository Repository des groupes
     * @param LevelRepository $levelRepository Repository des niveaux
     * @param ProgramRepository $programRepository Repository des programmes
     * @param EntityManagerInterface $em EntityManager Doctrine
     * @return JsonResponse JSON avec le groupe créé
     */
    #[Route('', methods: ['POST'])]
    public function create(
        Request $request,
        AcademicGroupRepository $academicGroupRepository,
        LevelRepository $levelRepository,
        ProgramRepository $programRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $group = new AcademicGroup();
        $group->setGroupNumber($data['groupNumber'] ?? 1);
        
        if (isset($data['level'])) {
            $level = $levelRepository->findOneBy(['code' => $data['level']]);
            if ($level) {
                $group->setLevel($level);
            }
        }
        
        if (isset($data['program'])) {
            $program = $programRepository->findOneBy(['code' => $data['program']]);
            if ($program) {
                $group->setProgram($program);
            }
        }

        $em->persist($group);
        $em->flush();

        return $this->json([
            'id' => $group->getId(),
            'groupNumber' => $group->getGroupNumber(),
            'level' => $group->getLevel()?->getCode(),
            'program' => $group->getProgram()?->getCode(),
        ], 201);
    }

    /**
     * Modifie un groupe académique existant
     * 
     * @param int $id Identifiant du groupe
     * @param Request $request Requête HTTP avec les données modifiées
     * @param AcademicGroupRepository $academicGroupRepository Repository des groupes
     * @param LevelRepository $levelRepository Repository des niveaux
     * @param ProgramRepository $programRepository Repository des programmes
     * @param EntityManagerInterface $em EntityManager Doctrine
     * @return JsonResponse JSON avec le groupe modifié ou 404
     */
    #[Route('/{id}', methods: ['PUT'])]
    public function update(
        int $id,
        Request $request,
        AcademicGroupRepository $academicGroupRepository,
        LevelRepository $levelRepository,
        ProgramRepository $programRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        $group = $academicGroupRepository->find($id);

        if (!$group) {
            return $this->json(['message' => 'AcademicGroup not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $group->setGroupNumber($data['groupNumber'] ?? $group->getGroupNumber());
        
        if (isset($data['level'])) {
            $level = $levelRepository->findOneBy(['code' => $data['level']]);
            if ($level) {
                $group->setLevel($level);
            }
        }
        
        if (isset($data['program'])) {
            $program = $programRepository->findOneBy(['code' => $data['program']]);
            if ($program) {
                $group->setProgram($program);
            }
        }

        $em->flush();

        return $this->json([
            'id' => $group->getId(),
            'groupNumber' => $group->getGroupNumber(),
            'level' => $group->getLevel()?->getCode(),
            'program' => $group->getProgram()?->getCode(),
        ]);
    }

    /**
     * Supprime un groupe académique
     * 
     * @param int $id Identifiant du groupe
     * @param AcademicGroupRepository $academicGroupRepository Repository des groupes
     * @param EntityManagerInterface $em EntityManager Doctrine
     * @return JsonResponse JSON de confirmation ou 404
     */
    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, AcademicGroupRepository $academicGroupRepository, EntityManagerInterface $em): JsonResponse
    {
        $group = $academicGroupRepository->find($id);

        if (!$group) {
            return $this->json(['message' => 'AcademicGroup not found'], 404);
        }

        $em->remove($group);
        $em->flush();

        return $this->json(['message' => 'AcademicGroup deleted successfully']);
    }
}
