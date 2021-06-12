<?php

namespace App\Repository;

use App\Entity\ReportStorage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReportStorage|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReportStorage|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReportStorage[]    findAll()
 * @method ReportStorage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportStorageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReportStorage::class);
    }

    // /**
    //  * @return ReportStorage[] Returns an array of ReportStorage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ReportStorage
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
