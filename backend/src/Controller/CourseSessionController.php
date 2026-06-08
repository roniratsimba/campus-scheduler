<?php

namespace App\Controller;

use App\Entity\AcademicGroup;
use App\Entity\CourseSession;
use App\Repository\CourseSessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

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