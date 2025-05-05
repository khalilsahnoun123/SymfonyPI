<?php

namespace App\Repository\covoiturage;

use App\Entity\covoiturage\Covoiturage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Covoiturage>
 */
class CovoiturageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Covoiturage::class);
    } 

    public function findAvailableCovoiturages()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.nbr_place > 0')
            ->getQuery()
            ->getResult();
    }

    public function findByDepartureAndDestination(string $departure, string $destination): array
{
    return $this->createQueryBuilder('c')
        ->andWhere('c.point_de_depart LIKE :departure')
        ->andWhere('c.point_de_destination LIKE :destination')
        ->setParameter('departure', '%' . $departure . '%')
        ->setParameter('destination', '%' . $destination . '%')
        ->getQuery()
        ->getResult();
}
}
