<?php

namespace App\Repository;

use App\Entity\CourseSession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Teacher;
use App\Entity\TimeSlot;
use App\Entity\ScheduleWeek;
use App\Entity\Room;
use App\Entity\AcademicGroup;

/**
 * Repository CourseSessionRepository - Accès aux données des séances de cours
 * 
 * Fournit des méthodes personnalisées pour la validation des conflits
 * et la recherche de séances par groupe, enseignant ou salle.
 * 
 * @author Campus Scheduler Team
 * @version 1.0
 * @extends ServiceEntityRepository<CourseSession>
 */
class CourseSessionRepository extends ServiceEntityRepository
{
    /**
     * Constructeur - Initialise le repository
     * 
     * @param ManagerRegistry $registry Registre des gestionnaires d'entités
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CourseSession::class);
    }

    /**
     * Vérifie si un enseignant a déjà un cours à un créneau donné
     * 
     * @param Teacher $teacher L'enseignant à vérifier
     * @param TimeSlot $timeSlot Le créneau horaire
     * @param ScheduleWeek $scheduleWeek La semaine d'emploi du temps
     * @param int|null $excludeSessionId ID de séance à exclure (pour les mises à jour)
     * @return bool True si conflit détecté
     */
    public function teacherConflict(
        Teacher $teacher,
        TimeSlot $timeSlot,
        ScheduleWeek $scheduleWeek,
        ?int $excludeSessionId = null
    ): bool {
        $qb = $this->createQueryBuilder('cs')
            ->select('COUNT(cs.id)')
            ->andWhere('cs.teacher = :teacher')
            ->andWhere('cs.timeSlot = :timeSlot')
            ->andWhere('cs.scheduleWeek = :week')
            ->setParameter('teacher', $teacher)
            ->setParameter('timeSlot', $timeSlot)
            ->setParameter('week', $scheduleWeek);

        if ($excludeSessionId) {
            $qb->andWhere('cs.id != :id')
                ->setParameter('id', $excludeSessionId);
        }

        return $qb->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * Vérifie si une salle est déjà occupée à un créneau donné
     * 
     * @param Room $room La salle à vérifier
     * @param TimeSlot $timeSlot Le créneau horaire
     * @param ScheduleWeek $scheduleWeek La semaine d'emploi du temps
     * @param int|null $excludeSessionId ID de séance à exclure (pour les mises à jour)
     * @return bool True si conflit détecté
     */
    public function roomConflict(
        Room $room,
        TimeSlot $timeSlot,
        ScheduleWeek $scheduleWeek,
        ?int $excludeSessionId = null
    ): bool {
        $qb = $this->createQueryBuilder('cs')
            ->select('COUNT(cs.id)')
            ->andWhere('cs.room = :room')
            ->andWhere('cs.timeSlot = :timeSlot')
            ->andWhere('cs.scheduleWeek = :week')
            ->setParameter('room', $room)
            ->setParameter('timeSlot', $timeSlot)
            ->setParameter('week', $scheduleWeek);

        if ($excludeSessionId) {
            $qb->andWhere('cs.id != :id')
                ->setParameter('id', $excludeSessionId);
        }

        return $qb->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * Vérifie si un groupe a déjà un cours à un créneau donné
     * 
     * @param AcademicGroup $group Le groupe à vérifier
     * @param TimeSlot $timeSlot Le créneau horaire
     * @param ScheduleWeek $scheduleWeek La semaine d'emploi du temps
     * @param int|null $excludeSessionId ID de séance à exclure (pour les mises à jour)
     * @return bool True si conflit détecté
     */
    public function groupConflict(
        AcademicGroup $group,
        TimeSlot $timeSlot,
        ScheduleWeek $scheduleWeek,
        ?int $excludeSessionId = null
    ): bool {
        $qb = $this->createQueryBuilder('cs')
            ->select('COUNT(cs.id)')
            ->join('cs.academicGroups', 'g')
            ->andWhere('g = :group')
            ->andWhere('cs.timeSlot = :timeSlot')
            ->andWhere('cs.scheduleWeek = :week')
            ->setParameter('group', $group)
            ->setParameter('timeSlot', $timeSlot)
            ->setParameter('week', $scheduleWeek);

        if ($excludeSessionId) {
            $qb->andWhere('cs.id != :id')
            ->setParameter('id', $excludeSessionId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * Retourne toutes les séances d'un groupe académique
     * 
     * @param int $groupId Identifiant du groupe
     * @return array Liste des séances du groupe
     */
    public function findByGroup(int $groupId): array
    {
        return $this->createQueryBuilder('cs')
            ->join('cs.academicGroups', 'g')
            ->andWhere('g.id = :groupId')
            ->setParameter('groupId', $groupId)
            ->orderBy('cs.timeSlot', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne toutes les séances d'un enseignant
     * 
     * @param int $teacherId Identifiant de l'enseignant
     * @return array Liste des séances de l'enseignant
     */
    public function findByTeacher(int $teacherId): array
    {
        return $this->createQueryBuilder('cs')
            ->andWhere('cs.teacher = :teacherId')
            ->setParameter('teacherId', $teacherId)
            ->orderBy('cs.timeSlot', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne toutes les séances d'une salle
     * 
     * @param int $roomId Identifiant de la salle
     * @return array Liste des séances dans la salle
     */
    public function findByRoom(int $roomId): array
    {
        return $this->createQueryBuilder('cs')
            ->andWhere('cs.room = :roomId')
            ->setParameter('roomId', $roomId)
            ->orderBy('cs.timeSlot', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les séances (des semaines publiées uniquement) d'un groupe
     * 
     * @param int $groupId Identifiant du groupe
     * @return array Liste des séances publiées du groupe
     */
    public function findPublishedByGroup(int $groupId): array
    {
        return $this->createQueryBuilder('cs')
            ->join('cs.academicGroups', 'g')
            ->join('cs.scheduleWeek', 'w')
            ->andWhere('g.id = :groupId')
            ->andWhere('w.status = :published')
            ->setParameter('groupId', $groupId)
            ->setParameter('published', 'PUBLISHED')
            ->orderBy('cs.timeSlot', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les séances (des semaines publiées uniquement) d'un enseignant
     * 
     * @param int $teacherId Identifiant de l'enseignant
     * @return array Liste des séances publiées de l'enseignant
     */
    public function findPublishedByTeacher(int $teacherId): array
    {
        return $this->createQueryBuilder('cs')
            ->join('cs.scheduleWeek', 'w')
            ->andWhere('cs.teacher = :teacherId')
            ->andWhere('w.status = :published')
            ->setParameter('teacherId', $teacherId)
            ->setParameter('published', 'PUBLISHED')
            ->orderBy('cs.timeSlot', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les séances (des semaines publiées uniquement) d'une salle
     * 
     * @param int $roomId Identifiant de la salle
     * @return array Liste des séances publiées dans la salle
     */
    public function findPublishedByRoom(int $roomId): array
    {
        return $this->createQueryBuilder('cs')
            ->join('cs.scheduleWeek', 'w')
            ->andWhere('cs.room = :roomId')
            ->andWhere('w.status = :published')
            ->setParameter('roomId', $roomId)
            ->setParameter('published', 'PUBLISHED')
            ->orderBy('cs.timeSlot', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Détecte les conflits de planification dans une semaine donnée.
     * 
     * Un conflit existe quand deux séances de la même semaine partagent
     * le même créneau (TimeSlot) avec :
     *  - le même enseignant ; ou
     *  - la même salle (BR-002, excepté pour les séances ONLINE) ; ou
     *  - le même groupe académique (BR-003).
     * 
     * @param ScheduleWeek $scheduleWeek La semaine à analyser
     * @return array Liste de messages décrivant chaque conflit détecté
     */
    public function weekConflicts(ScheduleWeek $scheduleWeek): array
    {
        $sessions = $this->findBy(['scheduleWeek' => $scheduleWeek]);

        $teacherMap = [];
        $roomMap = [];
        $groupMap = [];
        $conflicts = [];

        foreach ($sessions as $slotSession) {
            $teacherId = $slotSession->getTeacher()?->getId();
            $timeSlotId = $slotSession->getTimeSlot()?->getId();
            $roomId = $slotSession->getRoom()?->getId();
            $isOnline = $slotSession->getDeliveryMode()?->value === 'ONLINE';

            // Conflit enseignant (BR-001)
            $key = $teacherId.'-'.$timeSlotId;
            if (isset($teacherMap[$key]) && $teacherMap[$key] !== $slotSession->getId()) {
                $a = $teacherMap[$key];
                $conflicts[] = sprintf(
                    'Teacher (id %d) has two sessions (id %d and id %d) on the same time slot',
                    $teacherId, $a, $slotSession->getId()
                );
            } else {
                $teacherMap[$key] = $slotSession->getId();
            }

            // Conflit salle (BR-002), excepté ONLINE (les séances en ligne ne réservent pas de salle physique)
            if ($roomId !== null && !$isOnline) {
                $key = $roomId.'-'.$timeSlotId;
                if (isset($roomMap[$key]) && $roomMap[$key] !== $slotSession->getId()) {
                    $a = $roomMap[$key];
                    $conflicts[] = sprintf(
                        'Room (id %d) is used by two sessions (id %d and id %d) on the same time slot',
                        $roomId, $a, $slotSession->getId()
                    );
                } else {
                    $roomMap[$key] = $slotSession->getId();
                }
            }

            // Conflit groupe (BR-003)
            foreach ($slotSession->getAcademicGroups() as $group) {
                $key = $group->getId().'-'.$timeSlotId;
                if (isset($groupMap[$key]) && $groupMap[$key] !== $slotSession->getId()) {
                    $a = $groupMap[$key];
                    $conflicts[] = sprintf(
                        'Group (id %d) attends two sessions (id %d and id %d) on the same time slot',
                        $group->getId(), $a, $slotSession->getId()
                    );
                } else {
                    $groupMap[$key] = $slotSession->getId();
                }
            }
        }

        return array_values(array_unique($conflicts));
    }

    //    /**
    //     * @return CourseSession[] Returns an array of CourseSession objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?CourseSession
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
