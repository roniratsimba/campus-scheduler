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

/**
 * Contrôleur ScheduleWeekController - Gestion des semaines d'emploi du temps
 * 
 * Fournit les endpoints CRUD pour les semaines et les opérations
 * de publication et de copie d'emplois du temps.
 * Ces endpoints nécessitent une authentification admin.
 * 
 * Routes disponibles :
 * - GET /api/schedule-weeks : Liste toutes les semaines
 * - GET /api/schedule-weeks/{id} : Détails d'une semaine
 * - POST /api/schedule-weeks/{id}/publish : Publie une semaine
 * - POST /api/schedule-weeks/{id}/copy : Copie une semaine vers une autre
 * 
 * @author Campus Scheduler Team
 * @version 1.0
 */
#[Route('/api/schedule-weeks')]
final class ScheduleWeekController extends AbstractController
{
    /**
     * Liste toutes les semaines d'emploi du temps
     * 
     * @param ScheduleWeekRepository $scheduleWeekRepository Repository des semaines
     * @return JsonResponse JSON avec la liste des semaines
     */
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

    /**
     * Affiche les détails d'une semaine d'emploi du temps
     * 
     * @param int $id Identifiant de la semaine
     * @param ScheduleWeekRepository $scheduleWeekRepository Repository des semaines
     * @return JsonResponse JSON avec les détails de la semaine ou 404
     */
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

    /**
     * Publie une semaine d'emploi du temps
     * 
     * Change le statut de la semaine à PUBLISHED et enregistre la date de publication.
     * 
     * @param int $id Identifiant de la semaine à publier
     * @param ScheduleWeekRepository $scheduleWeekRepository Repository des semaines
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités
     * @return JsonResponse JSON avec le statut de publication
     */
    #[Route('/{id}/publish', methods: ['POST'])]
    public function publish(
        int $id,
        ScheduleWeekRepository $scheduleWeekRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        // Recherche de la semaine source
        $week = $scheduleWeekRepository->find($id);

        if (!$week) {
            return $this->json(
                ['message' => 'ScheduleWeek not found'],
                404
            );
        }

        // Mise à jour du statut et de la date de publication
        $week->setStatus('PUBLISHED');
        $week->setPublishedAt(new \DateTimeImmutable());
        
        // Sauvegarde en base de données
        $entityManager->flush();

        return $this->json([
            'id' => $week->getId(),
            'status' => $week->getStatus(),
            'publishedAt' => $week->getPublishedAt()?->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Copie toutes les séances d'une semaine vers une autre
     * 
     * Duplique toutes les séances de la semaine source vers la semaine cible.
     * Les séances copiées sont en statut DRAFT.
     * 
     * @param int $id Identifiant de la semaine source
     * @param Request $request Requête avec targetWeekId en JSON
     * @param ScheduleWeekRepository $scheduleWeekRepository Repository des semaines
     * @param CourseSessionRepository $sessionRepository Repository des séances
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités
     * @return JsonResponse JSON avec le nombre de séances copiées
     */
    #[Route('/{id}/copy', methods: ['POST'])]
    public function copy(
        int $id,
        Request $request,
        ScheduleWeekRepository $scheduleWeekRepository,
        CourseSessionRepository $sessionRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        // Recherche de la semaine source
        $sourceWeek = $scheduleWeekRepository->find($id);

        if (!$sourceWeek) {
            return $this->json(
                ['message' => 'ScheduleWeek not found'],
                404
            );
        }

        // Décodage des données JSON
        $data = json_decode($request->getContent(), true);
        $targetWeekId = $data['targetWeekId'] ?? null;

        // Validation du paramètre requis
        if (!$targetWeekId) {
            return $this->json(
                ['message' => 'targetWeekId is required'],
                400
            );
        }

        // Recherche de la semaine cible
        $targetWeek = $scheduleWeekRepository->find($targetWeekId);

        if (!$targetWeek) {
            return $this->json(
                ['message' => 'Target week not found'],
                404
            );
        }

        // Récupération des séances de la semaine source
        $sessions = $sessionRepository->findBy(['scheduleWeek' => $sourceWeek]);

        // Copie de chaque séance vers la semaine cible
        foreach ($sessions as $session) {
            $newSession = new CourseSession();
            $newSession->setTeacher($session->getTeacher());
            $newSession->setSubject($session->getSubject());
            $newSession->setRoom($session->getRoom());
            $newSession->setTimeSlot($session->getTimeSlot());
            $newSession->setScheduleWeek($targetWeek);
            $newSession->setStatus('DRAFT');
            $newSession->setDeliveryMode($session->getDeliveryMode());

            // Copie des groupes académiques
            foreach ($session->getAcademicGroups() as $group) {
                $newSession->addAcademicGroup($group);
            }

            $entityManager->persist($newSession);
        }

        // Sauvegarde en base de données
        $entityManager->flush();

        return $this->json([
            'message' => 'Week copied successfully',
            'sessionsCopied' => count($sessions),
        ]);
    }
}