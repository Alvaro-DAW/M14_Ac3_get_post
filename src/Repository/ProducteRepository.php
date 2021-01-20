<?php

namespace App\Repository;

use App\Entity\Producte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Producte|null find($id, $lockMode = null, $lockVersion = null)
 * @method Producte|null findOneBy(array $criteria, array $orderBy = null)
 * @method Producte[]    findAll()
 * @method Producte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProducteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Producte::class);
    }

    // /**
    //  * @return Producte[] Returns an array of Producte objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Producte
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
