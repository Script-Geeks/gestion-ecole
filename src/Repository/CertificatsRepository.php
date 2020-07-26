<?php

namespace App\Repository;

use App\Entity\Certificats;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Certificats|null find($id, $lockMode = null, $lockVersion = null)
 * @method Certificats|null findOneBy(array $criteria, array $orderBy = null)
 * @method Certificats[]    findAll()
 * @method Certificats[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CertificatsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Certificats::class);
    }

    // /**
    //  * @return Certificats[] Returns an array of Certificats objects
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
    public function findOneBySomeField($value): ?Certificats
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
