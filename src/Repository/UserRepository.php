<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function searchAndSort(string $search = '', string $sortBy = 'firstName', string $sortOrder = 'asc', ?int $excludeUserId = null)
    {
        $qb = $this->createQueryBuilder('u');

        if ($excludeUserId) {
            $qb->andWhere('u.id != :excludeUserId')
               ->setParameter('excludeUserId', $excludeUserId);
        }

        if ($search) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('u.firstName', ':search'),
                $qb->expr()->like('u.nom', ':search'),
                $qb->expr()->like('u.email', ':search'),
                $qb->expr()->like('u.phoneNumber', ':search'),
                $qb->expr()->like('u.gouvernorat', ':search'),
                $qb->expr()->like('u.municipalite', ':search'),
                $qb->expr()->like('u.adresse', ':search')
            ))
            ->setParameter('search', '%' . $search . '%');
        }

        // Validate sort field to prevent SQL injection
        $allowedSortFields = ['firstName', 'nom', 'email', 'phoneNumber', 'gouvernorat', 'municipalite', 'adresse', 'status', 'bloque'];
        $sortBy = in_array($sortBy, $allowedSortFields) ? $sortBy : 'firstName';
        $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';

        $qb->orderBy('u.' . $sortBy, $sortOrder);

        return $qb->getQuery()->getResult();
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
