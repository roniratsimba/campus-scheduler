<?php

namespace App\Controller;

use App\Repository\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Room;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contrôleur RoomController - Gestion des salles
 * 
 * Fournit les endpoints CRUD pour les salles de cours.
 * Ces endpoints nécessitent une authentification admin.
 * 
 * Routes disponibles :
 * - GET /api/rooms : Liste toutes les salles
 * - GET /api/rooms/{id} : Détails d'une salle
 * - POST /api/rooms : Crée une salle
 * - PUT /api/rooms/{id} : Modifie une salle
 * - DELETE /api/rooms/{id} : Supprime une salle
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

    /**
     * Crée une nouvelle salle
     * 
     * @param Request $request Requête HTTP avec les données de la salle
     * @param EntityManagerInterface $em EntityManager Doctrine
     * @return JsonResponse JSON avec la salle créée
     */
    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $room = new Room();
        $room->setCode($data['code'] ?? '');
        $room->setName($data['name'] ?? '');
        $room->setType($data['type'] ?? 'CLASSROOM');

        $em->persist($room);
        $em->flush();

        return $this->json([
            'id' => $room->getId(),
            'code' => $room->getCode(),
            'name' => $room->getName(),
            'type' => $room->getType(),
        ], 201);
    }

    /**
     * Modifie une salle existante
     * 
     * @param int $id Identifiant de la salle
     * @param Request $request Requête HTTP avec les données modifiées
     * @param RoomRepository $roomRepository Repository des salles
     * @param EntityManagerInterface $em EntityManager Doctrine
     * @return JsonResponse JSON avec la salle modifiée ou 404
     */
    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request, RoomRepository $roomRepository, EntityManagerInterface $em): JsonResponse
    {
        $room = $roomRepository->find($id);

        if (!$room) {
            return $this->json(['message' => 'Room not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $room->setCode($data['code'] ?? $room->getCode());
        $room->setName($data['name'] ?? $room->getName());
        $room->setType($data['type'] ?? $room->getType());

        $em->flush();

        return $this->json([
            'id' => $room->getId(),
            'code' => $room->getCode(),
            'name' => $room->getName(),
            'type' => $room->getType(),
        ]);
    }

    /**
     * Supprime une salle
     * 
     * @param int $id Identifiant de la salle
     * @param RoomRepository $roomRepository Repository des salles
     * @param EntityManagerInterface $em EntityManager Doctrine
     * @return JsonResponse JSON de confirmation ou 404
     */
    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, RoomRepository $roomRepository, EntityManagerInterface $em): JsonResponse
    {
        $room = $roomRepository->find($id);

        if (!$room) {
            return $this->json(['message' => 'Room not found'], 404);
        }

        $em->remove($room);
        $em->flush();

        return $this->json(['message' => 'Room deleted successfully']);
    }
}
