<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AcademicGroupRepository;
use App\Entity\AcademicGroup;

#[Route('/api/academic-groups')]
final class AcademicGroupController extends AbstractController
{
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
