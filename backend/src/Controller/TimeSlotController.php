<?php

namespace App\Controller;

use App\Repository\TimeSlotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\TimeSlot;

/**
 * Contrôleur TimeSlotController - Gestion des créneaux horaires
 * 
 * Fournit les endpoints CRUD pour les créneaux horaires.
 * Ces endpoints nécessitent une authentification admin.
 * 
 * Routes disponibles :
 * - GET /api/timeslots : Liste tous les créneaux
 * - GET /api/timeslots/{id} : Détails d'un créneau
 * 
 * @author Campus Scheduler Team
 * @version 1.0
 */
#[Route('/api/timeslots')]
final class TimeSlotController extends AbstractController
{
    /**
     * Liste tous les créneaux horaires
     * 
     * @param TimeSlotRepository $timeSlotRepository Repository des créneaux
     * @return JsonResponse JSON avec la liste des créneaux
     */
    #[Route('', methods: ['GET'])]
    public function index(TimeSlotRepository $timeSlotRepository): JsonResponse
    {
        $timeSlots = $timeSlotRepository->findAll();

        return $this->json(array_map(
            fn(TimeSlot $timeSlot) => [
                'id' => $timeSlot->getId(),
                'dayOfWeek' => $timeSlot->getDayOfWeek(),
                'startTime' => $timeSlot->getStartTime()?->format('H:i'),
                'endTime' => $timeSlot->getEndTime()?->format('H:i'),
            ],
            $timeSlots
        ));
    }

    /**
     * Affiche les détails d'un créneau horaire
     * 
     * @param int $id Identifiant du créneau
     * @param TimeSlotRepository $timeSlotRepository Repository des créneaux
     * @return JsonResponse JSON avec les détails du créneau ou 404
     */
    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, TimeSlotRepository $timeSlotRepository): JsonResponse
    {
        $timeSlot = $timeSlotRepository->find($id);

        if (!$timeSlot) {
            return $this->json(
                ['message' => 'TimeSlot not found'],
                404
            );
        }

        return $this->json([
            'id' => $timeSlot->getId(),
            'dayOfWeek' => $timeSlot->getDayOfWeek(),
            'startTime' => $timeSlot->getStartTime()?->format('H:i'),
            'endTime' => $timeSlot->getEndTime()?->format('H:i'),
        ]);
    }
}
