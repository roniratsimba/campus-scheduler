<?php

namespace App\Controller;

use App\Entity\AcademicGroup;
use App\Entity\CourseSession;
use App\Repository\CourseSessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

use App\Enum\DeliveryMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

use App\Repository\TeacherRepository;
use App\Repository\SubjectRepository;
use App\Repository\RoomRepository;
use App\Repository\TimeSlotRepository;
use App\Repository\AcademicGroupRepository;
use App\Repository\ScheduleWeekRepository;

#[Route('/api/course-sessions')]
final class CourseSessionController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(
        CourseSessionRepository $courseSessionRepository
    ): JsonResponse {
        $sessions = $courseSessionRepository->findAll();

        $data = array_map(
            fn (CourseSession $session) => $this->serializeSession($session),
            $sessions
        );

        return $this->json($data);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(
        int $id,
        CourseSessionRepository $courseSessionRepository
    ): JsonResponse {
        $session = $courseSessionRepository->find($id);

        if (!$session) {
            return $this->json(
                ['message' => 'Course session not found'],
                404
            );
        }

        return $this->json(
            $this->serializeSession($session)
        );
    }

    #[Route('', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        TeacherRepository $teacherRepository,
        SubjectRepository $subjectRepository,
        RoomRepository $roomRepository,
        TimeSlotRepository $timeSlotRepository,
        AcademicGroupRepository $academicGroupRepository,
        ScheduleWeekRepository $scheduleWeekRepository,
        CourseSessionRepository $courseSessionRepository,
    ): JsonResponse {

        $data = json_decode(
            $request->getContent(),
            true
        );

        $teacher = $teacherRepository->find(
            $data['teacherId'] ?? null
        );

        $subject = $subjectRepository->find(
            $data['subjectId'] ?? null
        );

        $timeSlot = $timeSlotRepository->find(
            $data['timeSlotId'] ?? null
        );

        $scheduleWeek = $scheduleWeekRepository->find(
            $data['scheduleWeekId'] ?? null
        );

        if (
            !$teacher ||
            !$subject ||
            !$timeSlot ||
            !$scheduleWeek
        ) {
            return $this->json([
                'message' => 'Invalid references',
                'teacher' => $teacher?->getId(),
                'subject' => $subject?->getId(),
                'timeSlot' => $timeSlot?->getId(),
                'scheduleWeek' => $scheduleWeek?->getId(),
            ], 400);
        }
        if (
            $courseSessionRepository->teacherConflict(
                $teacher,
                $timeSlot,
                $scheduleWeek
            )
        ) {
            return $this->json(
                [
                    'message' =>
                        'Teacher already assigned on this timeslot'
                ],
                400
            );
        }
        $session = new CourseSession();

        $session->setTeacher($teacher);
        $session->setSubject($subject);
        $session->setTimeSlot($timeSlot);
        $session->setScheduleWeek($scheduleWeek);

        if (!empty($data['roomId'])) {

            $room = $roomRepository->find(
                $data['roomId']
            );
            if (
                $room &&
                $courseSessionRepository->roomConflict(
                    $room,
                    $timeSlot,
                    $scheduleWeek
                )
            ) {
                return $this->json(
                    [
                        'message' =>
                            'Room already occupied'
                    ],
                    400
                );
            }

            if (!$room) {
                return $this->json([
                    'message' => 'Room not found'
                ], 400);
            }

            $session->setRoom($room);
        }

        foreach (
            $data['academicGroupIds'] ?? []
            as $groupId
        ) {

            $group = $academicGroupRepository->find(
                $groupId
            );

            if (!$group) {
                continue;
            }

            if (
                $courseSessionRepository->groupConflict(
                    $group,
                    $timeSlot,
                    $scheduleWeek
                )
            ) {
                return $this->json(
                    [
                        'message' =>
                            'Group already occupied'
                    ],
                    400
                );
            }

            $session->addAcademicGroup(
                $group
            );
        }
        $session->setStatus(
            $data['status'] ?? 'DRAFT'
        );

        $session->setDeliveryMode(
            DeliveryMode::from(
                $data['deliveryMode']
            )
        );

        $entityManager->persist($session);
        $entityManager->flush();

        return $this->json(
            $this->serializeSession($session),
            201
        );
    }
    #[Route('/{id}', methods: ['PUT'])]
    public function update(
        int $id,
        Request $request,
        CourseSessionRepository $courseSessionRepository,
        TeacherRepository $teacherRepository,
        SubjectRepository $subjectRepository,
        RoomRepository $roomRepository,
        TimeSlotRepository $timeSlotRepository,
        AcademicGroupRepository $academicGroupRepository,
        ScheduleWeekRepository $scheduleWeekRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {

        $session = $courseSessionRepository->find($id);

        if (!$session) {
            return $this->json(['message' => 'Course session not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $teacher = $teacherRepository->find($data['teacherId'] ?? null);
        $subject = $subjectRepository->find($data['subjectId'] ?? null);
        $timeSlot = $timeSlotRepository->find($data['timeSlotId'] ?? null);
        $scheduleWeek = $scheduleWeekRepository->find($data['scheduleWeekId'] ?? null);

        if (!$teacher || !$subject || !$timeSlot || !$scheduleWeek) {
            return $this->json(['message' => 'Invalid references'], 400);
        }

        // conflits teacher
        if (
            $courseSessionRepository->teacherConflict(
                $teacher,
                $timeSlot,
                $scheduleWeek,
                $session->getId()
            )
        ) {
            return $this->json(['message' => 'Teacher already assigned'], 400);
        }

        $session->setTeacher($teacher);
        $session->setSubject($subject);
        $session->setTimeSlot($timeSlot);
        $session->setScheduleWeek($scheduleWeek);

        // room
        $room = null;
        if (!empty($data['roomId'])) {
            $room = $roomRepository->find($data['roomId']);

            if (!$room) {
                return $this->json(['message' => 'Room not found'], 400);
            }

            if (
                $courseSessionRepository->roomConflict(
                    $room,
                    $timeSlot,
                    $scheduleWeek,
                    $session->getId()
                )
            ) {
                return $this->json(['message' => 'Room already occupied'], 400);
            }

            $session->setRoom($room);
        }

        // groups
        $session->getAcademicGroups()->clear();

        foreach ($data['academicGroupIds'] ?? [] as $groupId) {
            $group = $academicGroupRepository->find($groupId);

            if (!$group) {
                continue;
            }

            if (
                $courseSessionRepository->groupConflict(
                    $group,
                    $timeSlot,
                    $scheduleWeek,
                    $session->getId()
                )
            ) {
                return $this->json(['message' => 'Group already occupied'], 400);
            }

            $session->addAcademicGroup($group);
        }

        $session->setStatus($data['status'] ?? 'DRAFT');
        $session->setDeliveryMode(DeliveryMode::from($data['deliveryMode']));

        $entityManager->flush();

        return $this->json($this->serializeSession($session));
    }
    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(
        int $id,
        CourseSessionRepository $courseSessionRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {

        $session = $courseSessionRepository->find($id);

        if (!$session) {
            return $this->json([
                'message' => 'Course session not found'
            ], 404);
        }

        $entityManager->remove($session);
        $entityManager->flush();

        return $this->json([
            'message' => 'Course session deleted'
        ]);
    }
    private function serializeSession(
        CourseSession $session
    ): array {
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
                fn (AcademicGroup $group) =>
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