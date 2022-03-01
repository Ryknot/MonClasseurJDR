<?php

namespace App\Repository;

use App\Entity\Champs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Champs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Champs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Champs[]    findAll()
 * @method Champs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChampsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Champs::class);
    }

    // /**
    //  * @return Champs[] Returns an array of Champs objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Champs
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
