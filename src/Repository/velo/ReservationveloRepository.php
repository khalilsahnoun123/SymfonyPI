<?php

namespace App\Repository\velo;

use App\Entity\velo\Reservationvelo;
use App\Entity\velo\Velo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservationvelo>
 */
class ReservationveloRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservationvelo::class);
    }
    public function findOverlappingReservation(Velo $velo, \DateTimeInterface $start, \DateTimeInterface $end)
{
    return $this->createQueryBuilder('r')
        ->where('r.velo = :velo')
        ->andWhere('r.date_debut < :end AND r.date_fin > :start')
        ->setParameter('velo', $velo)
        ->setParameter('start', $start)
        ->setParameter('end', $end)
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();
}
    //    /**
    //     * @return Reservationvelo[] Returns an array of Reservationvelo objects
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

    //    public function findOneBySomeField($value): ?Reservationvelo
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
