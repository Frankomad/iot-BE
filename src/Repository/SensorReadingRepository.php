<?php

namespace App\Repository;

use App\Entity\Sensor;
use App\Entity\SensorReading;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SensorReading>
 */
class SensorReadingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SensorReading::class);
    }

    /**
     * @return SensorReading[]
     */
    public function getReadingsSinceTime(Sensor $sensor, \DateTime $time): array
    {
        return $this->createQueryBuilder('sr')
            ->andWhere('sr.readedAt >= :time')
            ->andWhere('sr.sensor = :sensor')
            ->setParameter('sensor', $sensor)
            ->setParameter('time', $time)
            ->getQuery()
            ->getResult();
    }

    public function getLastReading(Sensor $sensor): ?SensorReading
    {
        return $this->createQueryBuilder('sr')
            ->andWhere('sr.sensor = :sensor')
            ->setParameter('sensor', $sensor)
            ->orderBy('sr.readedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return SensorReading[] Returns an array of SensorReading objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?SensorReading
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
