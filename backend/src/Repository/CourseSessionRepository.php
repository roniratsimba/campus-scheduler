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
 * @extends ServiceEntityRepository<CourseSession>
 */
class CourseSessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CourseSession::class);
    }
    public function teacherConflict(
        Teacher $teacher,
        TimeSlot $timeSlot,
        ScheduleWeek $scheduleWeek
    ): bool {
        return $this->createQueryBuilder('cs')
            ->select('COUNT(cs.id)')
            ->andWhere('cs.teacher = :teacher')
            ->andWhere('cs.timeSlot = :timeSlot')
            ->andWhere('cs.scheduleWeek = :week')
            ->setParameter('teacher', $teacher)
            ->setParameter('timeSlot', $timeSlot)
            ->setParameter('week', $scheduleWeek)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    public function roomConflict(
        Room $room,
        TimeSlot $timeSlot,
        ScheduleWeek $scheduleWeek
    ): bool {
        return $this->createQueryBuilder('cs')
            ->select('COUNT(cs.id)')
            ->andWhere('cs.room = :room')
            ->andWhere('cs.timeSlot = :timeSlot')
            ->andWhere('cs.scheduleWeek = :week')
            ->setParameter('room', $room)
            ->setParameter('timeSlot', $timeSlot)
            ->setParameter('week', $scheduleWeek)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

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
