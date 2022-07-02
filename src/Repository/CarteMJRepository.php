<?php

namespace App\Repository;

use App\Entity\CarteMJ;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CarteMJ|null find($id, $lockMode = null, $lockVersion = null)
 * @method CarteMJ|null findOneBy(array $criteria, array $orderBy = null)
 * @method CarteMJ[]    findAll()
 * @method CarteMJ[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CarteMJRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarteMJ::class);
    }

    // /**
    //  * @return CarteMJ[] Returns an array of CarteMJ objects
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
    public function findOneBySomeField($value): ?CarteMJ
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
