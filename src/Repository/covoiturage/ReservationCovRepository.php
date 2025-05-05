<?php

namespace App\Repository\covoiturage;

use App\Entity\covoiturage\Covoiturage;              // Correct import for the Covoiturage entity
use App\Entity\covoiturage\ReservationCov;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReservationCov>
 */
class ReservationCovRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReservationCov::class);
    }

    /**
     * Find all reservations with their associated covoiturage.
     */
    public function findAllWithCovoiturage(): array
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.covoiturage', 'c')
            ->addSelect('c')
            ->getQuery()
            ->getResult();
    }
    // Method to count reservations by Covoiturage
    public function countByCovoiturage(Covoiturage $covoiturage): int
    {
        return $this->createQueryBuilder('r')
            ->select('count(r.id)')
            ->where('r.covoiturage = :covoiturage')
            ->setParameter('covoiturage', $covoiturage)
            ->getQuery()
            ->getSingleScalarResult(); // This returns a single value
    }
    //    /**
    //     * @return ReservationCov[] Returns an array of ReservationCov objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ReservationCov
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
