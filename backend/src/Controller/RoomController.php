<?php

namespace App\Controller;

use App\Repository\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Room;

/**
 * Contrôleur RoomController - Gestion des salles
 * 
 * Fournit les endpoints CRUD pour les salles de cours.
 * Ces endpoints nécessitent une authentification admin.
 * 
 * Routes disponibles :
 * - GET /api/rooms : Liste toutes les salles
 * - GET /api/rooms/{id} : Détails d'une salle
 * 
 * @author Campus Scheduler Team
 * @version 1.0
 */
#[Route('/api/rooms')]
final class RoomController extends AbstractController
{
    /**
     * Liste toutes les salles
     * 
     * @param RoomRepository $roomRepository Repository des salles
     * @return JsonResponse JSON avec la liste des salles
     */
    #[Route('', methods: ['GET'])]
    public function index(RoomRepository $roomRepository): JsonResponse
    {
        $rooms = $roomRepository->findAll();

        return $this->json(array_map(
            fn(Room $room) => [
                'id' => $room->getId(),
                'code' => $room->getCode(),
                'name' => $room->getName(),
                'type' => $room->getType(),
            ],
            $rooms
        ));
    }

    /**
     * Affiche les détails d'une salle
     * 
     * @param int $id Identifiant de la salle
     * @param RoomRepository $roomRepository Repository des salles
     * @return JsonResponse JSON avec les détails de la salle ou 404
     */
    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, RoomRepository $roomRepository): JsonResponse
    {
        $room = $roomRepository->find($id);

        if (!$room) {
            return $this->json(
                ['message' => 'Room not found'],
                404
            );
        }

        return $this->json([
            'id' => $room->getId(),
            'code' => $room->getCode(),
            'name' => $room->getName(),
            'type' => $room->getType(),
        ]);
    }
}
