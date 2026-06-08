<?php

namespace App\Controller;

use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/api/programs')]
final class ProgramController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(ProgramRepository $programRepository): JsonResponse
    {
        $programs = $programRepository->findAll();

        return $this->json($programs);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, ProgramRepository $programRepository): JsonResponse
    {
        $program = $programRepository->find($id);

        if(!$program) {
            return $this->json(
                ['message' => 'Program not found'],
                404
            );
        }

        return $this->json($program);
    }
}
