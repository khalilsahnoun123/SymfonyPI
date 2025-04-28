<?php

namespace App\Repository;

use App\Entity\Stationvelo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Stationvelo>
 */
class StationveloRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stationvelo::class);
    }

    public function findAllStations()
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.id_station', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
