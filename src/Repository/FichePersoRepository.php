<?php

namespace App\Repository;

use App\Entity\FichePerso;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FichePerso|null find($id, $lockMode = null, $lockVersion = null)
 * @method FichePerso|null findOneBy(array $criteria, array $orderBy = null)
 * @method FichePerso[]    findAll()
 * @method FichePerso[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FichePersoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FichePerso::class);
    }


    /*
    * @return \Doctrine\ORM\Query Returns an array of FichePerso objects
    */
    /*
    public function findchamps(int $id)
    {
         $queryBuilder =  $this->createQueryBuilder('f')
            ->join('f.champs', 'champs')
            ->addSelect('champs.typeInfo')
            ->andWhere('f.id = :id')
            ->setParameter('id', $id)
            ->groupBy('champs.typeInfo.label', 'ASC');

        return $queryBuilder->getQuery();
    }
    */


    /*
    public function findOneBySomeField($value): ?FichePerso
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
