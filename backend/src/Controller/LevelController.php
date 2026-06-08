<?php

namespace App\Controller;

use App\Repository\LevelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/levels')]
final class LevelController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(LevelRepository $levelRepository): JsonResponse
    {
        $levels = $levelRepository->findAll();

        return $this->json($levels);
    }

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

        return $this->json($level);
    }
}