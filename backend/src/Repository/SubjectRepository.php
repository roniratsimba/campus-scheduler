<?php

namespace App\Repository;

use App\Entity\Subject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository SubjectRepository - Accès aux données des matières
 * 
 * Fournit l'accès aux données des matières enseignées.
 * 
 * @author Campus Scheduler Team
 * @version 1.0
 * @extends ServiceEntityRepository<Subject>
 */
class SubjectRepository extends ServiceEntityRepository
{
    /**
     * Constructeur - Initialise le repository
     * 
     * @param ManagerRegistry $registry Registre des gestionnaires d'entités
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subject::class);
    }

    //    /**
    //     * @return Subject[] Returns an array of Subject objects
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

    //    public function findOneBySomeField($value): ?Subject
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
