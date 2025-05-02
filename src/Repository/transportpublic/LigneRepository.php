<?php

namespace App\Repository\transportpublic;

use App\Entity\transportpublic\Ligne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ligne>
 */
class LigneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ligne::class);
    }
     /**
     * @return Ligne[] Returns an array of Ligne objects whose name matches $term
     */
    public function findByName(string $term): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.name LIKE :term')
            ->setParameter('term', '%'.$term.'%')
            ->orderBy('l.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    //    /**
    //     * @return Ligne[] Returns an array of Ligne objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Ligne
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
