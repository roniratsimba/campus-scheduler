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

/**
 * Contrôleur CourseSessionController - Gestion des séances de cours
 * 
 * Fournit les endpoints CRUD pour les séances de cours avec validation
 * des conflits (enseignant, salle, groupe).
 * Ces endpoints nécessitent une authentification admin.
 * 
 * Routes disponibles :
 * - GET /api/course-sessions : Liste toutes les séances
 * - GET /api/course-sessions/{id} : Détails d'une séance
 * - POST /api/course-sessions : Crée une nouvelle séance
 * - PUT /api/course-sessions/{id} : Met à jour une séance
 * - DELETE /api/course-sessions/{id} : Supprime une séance
 * 
 * @author Campus Scheduler Team
 * @version 1.0
 */
#[Route('/api/course-sessions')]
final class CourseSessionController extends AbstractController
{
    /**
     * Liste toutes les séances de cours
     * 
     * @param CourseSessionRepository $courseSessionRepository Repository des séances
     * @return JsonResponse JSON avec la liste des séances sérialisées
     */
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

    /**
     * Affiche les détails d'une séance de cours
     * 
     * @param int $id Identifiant de la séance
     * @param CourseSessionRepository $courseSessionRepository Repository des séances
     * @return JsonResponse JSON avec les détails de la séance ou 404
     */
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

    /**
     * Crée une nouvelle séance de cours
     * 
     * Valide les conflits d'enseignant, de salle et de groupe avant création.
     * 
     * @param Request $request Requête avec les données de la séance en JSON
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités
     * @param TeacherRepository $teacherRepository Repository des enseignants
     * @param SubjectRepository $subjectRepository Repository des matières
     * @param RoomRepository $roomRepository Repository des salles
     * @param TimeSlotRepository $timeSlotRepository Repository des créneaux
     * @param AcademicGroupRepository $academicGroupRepository Repository des groupes
     * @param ScheduleWeekRepository $scheduleWeekRepository Repository des semaines
     * @param CourseSessionRepository $courseSessionRepository Repository des séances
     * @return JsonResponse JSON avec la séance créée ou erreur de validation
     */
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

        // Décodage des données JSON
        $data = json_decode(
            $request->getContent(),
            true
        );

        // Récupération des entités référencées
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

        // Validation des références obligatoires
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
        
        // Vérification du conflit enseignant
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
        
        // Création de la séance
        $session = new CourseSession();

        $session->setTeacher($teacher);
        $session->setSubject($subject);
        $session->setTimeSlot($timeSlot);
        $session->setScheduleWeek($scheduleWeek);

        // Gestion optionnelle de la salle avec vérification de conflit
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

        // Ajout des groupes académiques avec vérification de conflit
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
        
        // Définition du statut et du mode de livraison
        $session->setStatus(
            $data['status'] ?? 'DRAFT'
        );

        $session->setDeliveryMode(
            DeliveryMode::from(
                $data['deliveryMode']
            )
        );

        // Sauvegarde en base de données
        $entityManager->persist($session);
        $entityManager->flush();

        return $this->json(
            $this->serializeSession($session),
            201
        );
    }
    /**
     * Met à jour une séance de cours existante
     * 
     * Valide les conflits d'enseignant, de salle et de groupe avant mise à jour.
     * 
     * @param int $id Identifiant de la séance
     * @param Request $request Requête avec les données de mise à jour en JSON
     * @param CourseSessionRepository $courseSessionRepository Repository des séances
     * @param TeacherRepository $teacherRepository Repository des enseignants
     * @param SubjectRepository $subjectRepository Repository des matières
     * @param RoomRepository $roomRepository Repository des salles
     * @param TimeSlotRepository $timeSlotRepository Repository des créneaux
     * @param AcademicGroupRepository $academicGroupRepository Repository des groupes
     * @param ScheduleWeekRepository $scheduleWeekRepository Repository des semaines
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités
     * @return JsonResponse JSON avec la séance mise à jour ou erreur de validation
     */
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

        // Recherche de la séance existante
        $session = $courseSessionRepository->find($id);

        if (!$session) {
            return $this->json(['message' => 'Course session not found'], 404);
        }

        // Décodage des données JSON
        $data = json_decode($request->getContent(), true);

        // Récupération des entités référencées
        $teacher = $teacherRepository->find($data['teacherId'] ?? null);
        $subject = $subjectRepository->find($data['subjectId'] ?? null);
        $timeSlot = $timeSlotRepository->find($data['timeSlotId'] ?? null);
        $scheduleWeek = $scheduleWeekRepository->find($data['scheduleWeekId'] ?? null);

        // Validation des références obligatoires
        if (!$teacher || !$subject || !$timeSlot || !$scheduleWeek) {
            return $this->json(['message' => 'Invalid references'], 400);
        }

        // Vérification du conflit enseignant (en excluant la séance actuelle)
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

        // Gestion de la salle avec vérification de conflit
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

        // Remplacement des groupes académiques avec vérification de conflit
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

        // Mise à jour du statut et du mode de livraison
        $session->setStatus($data['status'] ?? 'DRAFT');
        $session->setDeliveryMode(DeliveryMode::from($data['deliveryMode']));

        // Sauvegarde en base de données
        $entityManager->flush();

        return $this->json($this->serializeSession($session));
    }
    /**
     * Supprime une séance de cours
     * 
     * @param int $id Identifiant de la séance
     * @param CourseSessionRepository $courseSessionRepository Repository des séances
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités
     * @return JsonResponse JSON de confirmation ou 404
     */
    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(
        int $id,
        CourseSessionRepository $courseSessionRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {

        // Recherche de la séance à supprimer
        $session = $courseSessionRepository->find($id);

        if (!$session) {
            return $this->json([
                'message' => 'Course session not found'
            ], 404);
        }

        // Suppression de la séance
        $entityManager->remove($session);
        $entityManager->flush();

        return $this->json([
            'message' => 'Course session deleted'
        ]);
    }
    /**
     * Sérialise une séance de cours en tableau JSON
     * 
     * Convertit l'objet CourseSession en tableau associatif
     * pour l'envoi via l'API JSON.
     * 
     * @param CourseSession $session La séance à sérialiser
     * @return array Tableau représentant la séance
     */
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