<?php

namespace App\Controller;

use App\Repository\ScheduleWeekRepository;
use App\Repository\CourseSessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\ScheduleWeek;
use App\Entity\CourseSession;
use Doctrine\ORM\EntityManagerInterface;

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

    #[Route('/{id}/publish', methods: ['POST'])]
    public function publish(
        int $id,
        ScheduleWeekRepository $scheduleWeekRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $week = $scheduleWeekRepository->find($id);

        if (!$week) {
            return $this->json(
                ['message' => 'ScheduleWeek not found'],
                404
            );
        }

        $week->setStatus('PUBLISHED');
        $week->setPublishedAt(new \DateTimeImmutable());
        
        $entityManager->flush();

        return $this->json([
            'id' => $week->getId(),
            'status' => $week->getStatus(),
            'publishedAt' => $week->getPublishedAt()?->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/{id}/copy', methods: ['POST'])]
    public function copy(
        int $id,
        Request $request,
        ScheduleWeekRepository $scheduleWeekRepository,
        CourseSessionRepository $sessionRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $sourceWeek = $scheduleWeekRepository->find($id);

        if (!$sourceWeek) {
            return $this->json(
                ['message' => 'ScheduleWeek not found'],
                404
            );
        }

        $data = json_decode($request->getContent(), true);
        $targetWeekId = $data['targetWeekId'] ?? null;

        if (!$targetWeekId) {
            return $this->json(
                ['message' => 'targetWeekId is required'],
                400
            );
        }

        $targetWeek = $scheduleWeekRepository->find($targetWeekId);

        if (!$targetWeek) {
            return $this->json(
                ['message' => 'Target week not found'],
                404
            );
        }

        $sessions = $sessionRepository->findBy(['scheduleWeek' => $sourceWeek]);

        foreach ($sessions as $session) {
            $newSession = new CourseSession();
            $newSession->setTeacher($session->getTeacher());
            $newSession->setSubject($session->getSubject());
            $newSession->setRoom($session->getRoom());
            $newSession->setTimeSlot($session->getTimeSlot());
            $newSession->setScheduleWeek($targetWeek);
            $newSession->setStatus('DRAFT');
            $newSession->setDeliveryMode($session->getDeliveryMode());

            foreach ($session->getAcademicGroups() as $group) {
                $newSession->addAcademicGroup($group);
            }

            $entityManager->persist($newSession);
        }

        $entityManager->flush();

        return $this->json([
            'message' => 'Week copied successfully',
            'sessionsCopied' => count($sessions),
        ]);
    }
}