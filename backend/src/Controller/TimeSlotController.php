<?php

namespace App\Controller;

use App\Repository\TimeSlotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api/timeslots')]
final class TimeSlotController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(TimeSlotRepository $timeSlotRepository): JsonResponse
    {
        $timeSlots = $timeSlotRepository->findAll();

        return $this->json($timeSlots);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, TimeSlotRepository $timeSlotRepository): JsonResponse
    {
        $timeSlot = $timeSlotRepository->find($id);

        if(!$timeSlot) {
            return $this->json(
                ['message' => 'TimeSlot not found'],
                404  
            );
        }

        return $this->json($timeSlot);
    }
}
