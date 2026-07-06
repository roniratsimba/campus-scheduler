<?php

namespace App\Controller;

use App\Repository\AcademicGroupRepository;
use App\Repository\CourseSessionRepository;
use App\Repository\RoomRepository;
use App\Repository\TeacherRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/public')]
class PublicScheduleController extends AbstractController
{
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
                'type' => $room->getRoomType()?->getName(),
            ],
            'sessions' => array_map(
                fn ($session) => $this->serializeSession($session),
                $sessions
            )
        ]);
    }

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

    #[Route('/rooms', methods: ['GET'])]
    public function listRooms(RoomRepository $roomRepository): JsonResponse
    {
        $rooms = $roomRepository->findAll();

        return $this->json(array_map(
            fn ($room) => [
                'id' => $room->getId(),
                'name' => $room->getName(),
                'code' => $room->getCode(),
                'type' => $room->getRoomType()?->getName(),
            ],
            $rooms
        ));
    }

    #[Route('/rooms/free', methods: ['GET'])]
    public function findFreeRooms(
        Request $request,
        RoomRepository $roomRepository,
        CourseSessionRepository $sessionRepository
    ): JsonResponse {
        $dayOfWeek = $request->query->get('dayOfWeek');
        $startTime = $request->query->get('startTime');
        $endTime = $request->query->get('endTime');
        $weekId = $request->query->get('weekId');

        if (!$dayOfWeek || !$startTime || !$endTime || !$weekId) {
            return $this->json(['message' => 'Missing required parameters'], 400);
        }

        $allRooms = $roomRepository->findAll();
        $occupiedRoomIds = [];

        // Find rooms occupied during the specified time slot
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

        foreach ($sessions as $session) {
            if ($session->getRoom()) {
                $occupiedRoomIds[] = $session->getRoom()->getId();
            }
        }

        // Filter out occupied rooms
        $freeRooms = array_filter($allRooms, function ($room) use ($occupiedRoomIds) {
            return !in_array($room->getId(), $occupiedRoomIds);
        });

        return $this->json(array_map(
            fn ($room) => [
                'id' => $room->getId(),
                'name' => $room->getName(),
                'code' => $room->getCode(),
                'type' => $room->getRoomType()?->getName(),
            ],
            $freeRooms
        ));
    }

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
