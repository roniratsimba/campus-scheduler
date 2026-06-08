<?php

namespace App\Controller;

use App\Repository\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Room;

#[Route('/api/rooms')]
final class RoomController extends AbstractController
{
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
