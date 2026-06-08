<?php

namespace App\Controller;

use App\Repository\ScheduleWeekRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\ScheduleWeek;

#[Route('/api/schedule-weeks')]
final class ScheduleWeekController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(ScheduleWeekRepository $scheduleWeekRepository): JsonResponse
    {
        $weeks = $scheduleWeekRepository->findAll();

        return $this->json(array_map(
            fn(ScheduleWeek $week) => [
                'id' => $week->getId(),
                'startDate' => $week->getStartDate()?->format('Y-m-d'),
                'endDate' => $week->getEndDate()?->format('Y-m-d'),
                'status' => $week->getStatus(),
                'publishedAt' => $week->getPublishedAt()?->format('Y-m-d H:i:s'),
            ],
            $weeks
        ));
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, ScheduleWeekRepository $scheduleWeekRepository): JsonResponse
    {
        $week = $scheduleWeekRepository->find($id);

        if (!$week) {
            return $this->json(
                ['message' => 'ScheduleWeek not found'],
                404
            );
        }

        return $this->json([
            'id' => $week->getId(),
            'startDate' => $week->getStartDate()?->format('Y-m-d'),
            'endDate' => $week->getEndDate()?->format('Y-m-d'),
            'status' => $week->getStatus(),
            'publishedAt' => $week->getPublishedAt()?->format('Y-m-d H:i:s'),
        ]);
    }
}