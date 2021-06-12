<?php

namespace App\Repository;

use App\Entity\LikeStorage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LikeStorage|null find($id, $lockMode = null, $lockVersion = null)
 * @method LikeStorage|null findOneBy(array $criteria, array $orderBy = null)
 * @method LikeStorage[]    findAll()
 * @method LikeStorage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikeStorageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LikeStorage::class);
    }

    // /**
    //  * @return LikeStorage[] Returns an array of LikeStorage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LikeStorage
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
