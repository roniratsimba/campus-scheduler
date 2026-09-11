<?php

namespace App\Repository;

use App\Entity\TimeSlot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository TimeSlotRepository - Accès aux données des créneaux horaires
 * 
 * Fournit l'accès aux données des créneaux horaires.
 * 
 * @author Campus Scheduler Team
 * @version 1.0
 * @extends ServiceEntityRepository<TimeSlot>
 */
class TimeSlotRepository extends ServiceEntityRepository
{
    /**
     * Constructeur - Initialise le repository
     * 
     * @param ManagerRegistry $registry Registre des gestionnaires d'entités
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeSlot::class);
    }

//    /**
//     * @return TimeSlot[] Returns an array of TimeSlot objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TimeSlot
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
