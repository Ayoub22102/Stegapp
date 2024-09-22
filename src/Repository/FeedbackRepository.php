<?php

namespace App\Repository;

use App\Entity\Feedback;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Feedback>
 */
class FeedbackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feedback::class);
    }

    // Example method to find feedback by reclamation
    public function findOneByReclamation($reclamation): ?Feedback
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.reclamation = :reclamation')
            ->setParameter('reclamation', $reclamation)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
