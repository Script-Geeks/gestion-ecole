<?php

namespace App\Repository;

use App\Entity\Jours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Jours|null find($id, $lockMode = null, $lockVersion = null)
 * @method Jours|null findOneBy(array $criteria, array $orderBy = null)
 * @method Jours[]    findAll()
 * @method Jours[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Jours::class);
    }

    // /**
    //  * @return Jours[] Returns an array of Jours objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Jours
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
