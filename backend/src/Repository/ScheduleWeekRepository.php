<?php

namespace App\Repository;

use App\Entity\ScheduleWeek;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository ScheduleWeekRepository - Accès aux données des semaines d'emploi du temps
 * 
 * Fournit l'accès aux données des semaines d'emploi du temps.
 * 
 * @author Campus Scheduler Team
 * @version 1.0
 * @extends ServiceEntityRepository<ScheduleWeek>
 */
class ScheduleWeekRepository extends ServiceEntityRepository
{
    /**
     * Constructeur - Initialise le repository
     * 
     * @param ManagerRegistry $registry Registre des gestionnaires d'entités
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScheduleWeek::class);
    }

//    /**
//     * @return ScheduleWeek[] Returns an array of ScheduleWeek objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ScheduleWeek
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
