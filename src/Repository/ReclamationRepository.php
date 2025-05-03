<?php

// src/Repository/ReclamationRepository.php

namespace App\Repository;

use App\Entity\Reclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

class ReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamation::class);
    }

    public function findFiltered(?string $query, ?string $type, ?string $status): array
    {
        $qb = $this->createQueryBuilder('r');

        if ($query) {
            $qb->andWhere('r.utilisateurId LIKE :query OR r.id LIKE :query')
                ->setParameter('query', '%'.$query.'%');
        }

        if ($type) {
            $qb->andWhere('r.type = :type')
                ->setParameter('type', $type);
        }

        if ($status) {
            $qb->andWhere('r.status = :status')
                ->setParameter('status', $status);
        }

        return $qb->getQuery()->getResult();
    }
}
