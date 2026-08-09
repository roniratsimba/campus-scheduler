<?php

namespace App\Controller;

use App\Repository\AcademicGroupRepository;
use App\Repository\CourseSessionRepository;
use App\Repository\RoomRepository;
use App\Repository\TeacherRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur PublicScheduleController - API publique pour l'emploi du temps
 * 
 * Fournit des endpoints publics pour consulter les emplois du temps
 * sans authentification. Accessible aux étudiants et enseignants.
 * 
 * Routes disponibles :
 * - GET /api/public/schedule/group/{id} : Emploi du temps d'un groupe
 * - GET /api/public/schedule/teacher/{id} : Emploi du temps d'un enseignant
 * - GET /api/public/schedule/room/{id} : Emploi du temps d'une salle
 * - GET /api/public/groups : Liste de tous les groupes
 * - GET /api/public/teachers : Liste de tous les enseignants
 * - GET /api/public/rooms : Liste de toutes les salles
 * - GET /api/public/rooms/free : Salles libres à un créneau donné
 * 
 * @author Campus Scheduler Team
 * @version 1.0
 */
#[Route('/api/public')]
class PublicScheduleController extends AbstractController
{
    /**
     * Retourne l'emploi du temps d'un groupe académique
     * 
     * @param int $id Identifiant du groupe
     * @param AcademicGroupRepository $groupRepository Repository des groupes
     * @param CourseSessionRepository $sessionRepository Repository des séances
     * @return JsonResponse JSON avec les infos du groupe et ses séances
     */
    #[Route('/schedule/group/{id}', methods: ['GET'])]
    public function getGroupSchedule(
        int $id,
        AcademicGroupRepository $groupRepository,
        CourseSessionRepository $sessionRepository
    ): JsonResponse {
        $group = $groupRepository->find($id);

        if (!$group) {
            return $this->json(['message' => 'Group not found'], 404);
        }

        $sessions = $sessionRepository->findByGroup($id);

        return $this->json([
            'group' => [
                'id' => $group->getId(),
                'displayName' => $group->getDisplayName(),
                'level' => $group->getLevel()?->getCode(),
                'program' => $group->getProgram()?->getCode(),
            ],
            'sessions' => array_map(
                fn ($session) => $this->serializeSession($session),
                $sessions
            )
        ]);
    }

    /**
     * Retourne l'emploi du temps d'un enseignant
     * 
     * @param int $id Identifiant de l'enseignant
     * @param TeacherRepository $teacherRepository Repository des enseignants
     * @param CourseSessionRepository $sessionRepository Repository des séances
     * @return JsonResponse JSON avec les infos de l'enseignant et ses séances
     */
    #[Route('/schedule/teacher/{id}', methods: ['GET'])]
    public function getTeacherSchedule(
        int $id,
        TeacherRepository $teacherRepository,
        CourseSessionRepository $sessionRepository
    ): JsonResponse {
        $teacher = $teacherRepository->find($id);

        if (!$teacher) {
            return $this->json(['message' => 'Teacher not found'], 404);
        }

        $sessions = $sessionRepository->findByTeacher($id);

        return $this->json([
            'teacher' => [
                'id' => $teacher->getId(),
                'firstName' => $teacher->getFirstName(),
                'lastName' => $teacher->getLastName(),
                'email' => $teacher->getEmail(),
            ],
            'sessions' => array_map(
                fn ($session) => $this->serializeSession($session),
                $sessions
            )
        ]);
    }

    /**
     * Retourne l'emploi du temps d'une salle
     * 
     * @param int $id Identifiant de la salle
     * @param RoomRepository $roomRepository Repository des salles
     * @param CourseSessionRepository $sessionRepository Repository des séances
     * @return JsonResponse JSON avec les infos de la salle et ses séances
     */
    #[Route('/schedule/room/{id}', methods: ['GET'])]
    public function getRoomSchedule(
        int $id,
        RoomRepository $roomRepository,
        CourseSessionRepository $sessionRepository
    ): JsonResponse {
        $room = $roomRepository->find($id);

        if (!$room) {
            return $this->json(['message' => 'Room not found'], 404);
        }

        $sessions = $sessionRepository->findByRoom($id);

        return $this->json([
            'room' => [
                'id' => $room->getId(),
                'name' => $room->getName(),
                'code' => $room->getCode(),
                'type' => $room->getType(),
            ],
            'sessions' => array_map(
                fn ($session) => $this->serializeSession($session),
                $sessions
            )
        ]);
    }

    /**
     * Liste tous les groupes académiques
     * 
     * @param AcademicGroupRepository $groupRepository Repository des groupes
     * @return JsonResponse JSON avec la liste des groupes
     */
    #[Route('/groups', methods: ['GET'])]
    public function listGroups(AcademicGroupRepository $groupRepository): JsonResponse
    {
        $groups = $groupRepository->findAll();

        return $this->json(array_map(
            fn ($group) => [
                'id' => $group->getId(),
                'displayName' => $group->getDisplayName(),
                'level' => $group->getLevel()?->getCode(),
                'program' => $group->getProgram()?->getCode(),
            ],
            $groups
        ));
    }

    /**
     * Liste tous les enseignants
     * 
     * @param TeacherRepository $teacherRepository Repository des enseignants
     * @return JsonResponse JSON avec la liste des enseignants
     */
    #[Route('/teachers', methods: ['GET'])]
    public function listTeachers(TeacherRepository $teacherRepository): JsonResponse
    {
        $teachers = $teacherRepository->findAll();

        return $this->json(array_map(
            fn ($teacher) => [
                'id' => $teacher->getId(),
                'firstName' => $teacher->getFirstName(),
                'lastName' => $teacher->getLastName(),
                'email' => $teacher->getEmail(),
            ],
            $teachers
        ));
    }

    /**
     * Liste toutes les salles
     * 
     * @param RoomRepository $roomRepository Repository des salles
     * @return JsonResponse JSON avec la liste des salles
     */
    #[Route('/rooms', methods: ['GET'])]
    public function listRooms(RoomRepository $roomRepository): JsonResponse
    {
        $rooms = $roomRepository->findAll();

        return $this->json(array_map(
            fn ($room) => [
                'id' => $room->getId(),
                'name' => $room->getName(),
                'code' => $room->getCode(),
                'type' => $room->getType(),
            ],
            $rooms
        ));
    }

    /**
     * Trouve les salles libres à un créneau horaire donné
     * 
     * Paramètres query requis :
     * - dayOfWeek : Jour de la semaine (ex: MONDAY)
     * - startTime : Heure de début (ex: 08:00)
     * - endTime : Heure de fin (ex: 10:00)
     * - weekId : Identifiant de la semaine
     * 
     * @param Request $request Requête HTTP avec les paramètres query
     * @param RoomRepository $roomRepository Repository des salles
     * @param CourseSessionRepository $sessionRepository Repository des séances
     * @return JsonResponse JSON avec la liste des salles libres
     */
    #[Route('/rooms/free', methods: ['GET'])]
    public function findFreeRooms(
        Request $request,
        RoomRepository $roomRepository,
        CourseSessionRepository $sessionRepository
    ): JsonResponse {
        // Récupération des paramètres de la requête
        $dayOfWeek = $request->query->get('dayOfWeek');
        $startTime = $request->query->get('startTime');
        $endTime = $request->query->get('endTime');
        $weekId = $request->query->get('weekId');

        // Validation des paramètres requis
        if (!$dayOfWeek || !$startTime || !$endTime || !$weekId) {
            return $this->json(['message' => 'Missing required parameters'], 400);
        }

        // Récupération de toutes les salles
        $allRooms = $roomRepository->findAll();
        $occupiedRoomIds = [];

        // Recherche des salles occupées pendant le créneau spécifié
        $sessions = $sessionRepository->createQueryBuilder('cs')
            ->join('cs.timeSlot', 'ts')
            ->join('cs.room', 'r')
            ->andWhere('ts.dayOfWeek = :dayOfWeek')
            ->andWhere('ts.startTime = :startTime')
            ->andWhere('ts.endTime = :endTime')
            ->andWhere('cs.scheduleWeek = :weekId')
            ->setParameter('dayOfWeek', $dayOfWeek)
            ->setParameter('startTime', new \DateTime($startTime))
            ->setParameter('endTime', new \DateTime($endTime))
            ->setParameter('weekId', $weekId)
            ->getQuery()
            ->getResult();

        // Collecte des identifiants des salles occupées
        foreach ($sessions as $session) {
            if ($session->getRoom()) {
                $occupiedRoomIds[] = $session->getRoom()->getId();
            }
        }

        // Filtrage pour ne garder que les salles libres
        $freeRooms = array_filter($allRooms, function ($room) use ($occupiedRoomIds) {
            return !in_array($room->getId(), $occupiedRoomIds);
        });

        return $this->json(array_map(
            fn ($room) => [
                'id' => $room->getId(),
                'name' => $room->getName(),
                'code' => $room->getCode(),
                'type' => $room->getType(),
            ],
            $freeRooms
        ));
    }

    /**
     * Sérialise une séance de cours en tableau JSON
     * 
     * Convertit l'objet CourseSession en tableau associatif
     * pour l'envoi via l'API JSON.
     * 
     * @param mixed $session La séance à sérialiser
     * @return array Tableau représentant la séance
     */
    private function serializeSession($session): array
    {
        return [
            'id' => $session->getId(),
            'teacher' => trim(
                ($session->getTeacher()?->getFirstName() ?? '')
                .' '.
                ($session->getTeacher()?->getLastName() ?? '')
            ),
            'subject' => $session->getSubject()?->getName(),
            'room' => $session->getRoom()?->getCode(),
            'deliveryMode' => $session->getDeliveryMode()?->value,
            'dayOfWeek' => $session->getTimeSlot()?->getDayOfWeek(),
            'startTime' => $session
                ->getTimeSlot()
                ?->getStartTime()
                ?->format('H:i'),
            'endTime' => $session
                ->getTimeSlot()
                ?->getEndTime()
                ?->format('H:i'),
            'groups' => array_map(
                fn ($group) =>
                    $group->getLevel()?->getCode()
                    .' '.
                    $group->getProgram()?->getCode()
                    .' G'.
                    $group->getGroupNumber(),
                $session->getAcademicGroups()->toArray()
            ),
            'weekId' => $session
                ->getScheduleWeek()
                ?->getId(),
            'status' => $session->getStatus(),
        ];
    }
}
