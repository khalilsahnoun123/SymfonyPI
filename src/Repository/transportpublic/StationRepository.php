<?php

namespace App\Repository\transportpublic;

use App\Entity\transportpublic\Station;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Station>
 */
class StationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Station::class);
    }
    public function findByNom(string $nom): array
{
    return $this->createQueryBuilder('s')
        ->where('LOWER(s.nom) LIKE :nom')
        ->setParameter('nom', '%' . strtolower($nom) . '%')
        ->orderBy('s.nom', 'ASC')
        ->getQuery()
        ->getResult();
}

    //    /**
    //     * @return Station[] Returns an array of Station objects
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

    //    public function findOneBySomeField($value): ?Station
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
