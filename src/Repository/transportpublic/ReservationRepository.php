<?php

namespace App\Repository\transportpublic;

use App\Entity\transportpublic\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }
 // src/Repository/ReservationRepository.php

public function findByFilters(?string $category, ?string $status, ?\DateTimeInterface $from, ?\DateTimeInterface $to): array
{
    $qb = $this->createQueryBuilder('r');

    if ($category) {
        $qb->andWhere('r.ticket_category = :cat')
           ->setParameter('cat', $category);
    }

    if ($status) {
        $qb->andWhere('r.status = :stat')
           ->setParameter('stat', $status);
    }

    if ($from) {
        $qb->andWhere('r.travel_date >= :from')
           ->setParameter('from', $from->setTime(0,0,0));
    }

    if ($to) {
        $qb->andWhere('r.travel_date <= :to')
           ->setParameter('to', $to->setTime(23,59,59));
    }

    return $qb
        ->orderBy('r.travel_date','DESC')
        ->getQuery()
        ->getResult();
}



    //    /**
    //     * @return Reservation[] Returns an array of Reservation objects
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

    //    public function findOneBySomeField($value): ?Reservation
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
