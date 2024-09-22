<?php

namespace App\Repository;

use App\Entity\Reclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reclamation>
 */
class ReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamation::class);
    }

    public function findByClientMail(string $clientmail): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.clientmail = :clientmail')
            ->setParameter('clientmail', $clientmail)
            ->orderBy('r.date_soumission', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findPendingReclamationsSortedByClient(): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.etat_validation = :status')
            ->setParameter('status', 'en attente')
            ->orderBy('r.clientmail', 'ASC')
            ->getQuery()
            ->getResult();
    }
      /**
     * Find reclamations with interventions in the 'Clôturé' state.
     * @param string $clientMail - The email of the client.
     * @return Reclamation[] Returns an array of Reclamation objects with "Clôturé" interventions.
     */
    public function findByClientAndCloturedInterventions(string $clientMail): array
    {
        // Build a query to find reclamations with interventions having 'Clôturé' state
        return $this->createQueryBuilder('r')
            ->innerJoin('r.affectation_technicien', 't') // Join to associate with technicians
            ->innerJoin('App\Entity\Intervention', 'i', 'WITH', 'i.id = r.id') // Join Intervention which inherits Reclamation
            ->andWhere('i.etat = :etat') // Ensure intervention is in "Clôturé" state
            ->andWhere('r.clientmail = :clientMail') // Ensure reclamation belongs to the client
            ->setParameter('etat', 'Clôturé')
            ->setParameter('clientMail', $clientMail)
            ->getQuery()
            ->getResult();
    }
}
