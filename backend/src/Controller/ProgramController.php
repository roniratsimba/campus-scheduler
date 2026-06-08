<?php

namespace App\Controller;

use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Program;


#[Route('/api/programs')]
final class ProgramController extends AbstractController
{
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
