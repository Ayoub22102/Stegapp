<?php

namespace App\Controller;

use App\Entity\Intervention;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur pour la gestion des interventions par les techniciens.
 */
#[Route('/technicien', name: 'technicien_')]
class TechnicienController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/planning', name: 'planning', methods: ['GET'])]
    public function showPlanning(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof \App\Entity\Technicien) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        // Récupère toutes les interventions non terminées (état = 'en cours') assignées à ce technicien
        $reclamations = $this->entityManager->getRepository(Intervention::class)->createQueryBuilder('i')
            ->join('i.affectation_technicien', 't')
            ->where('t.id = :technicienId')
            ->andWhere('i.etat = :etat')
            ->setParameter('technicienId', $user->getId())
            ->setParameter('etat', 'en cours')
            ->getQuery()
            ->getResult();

        return $this->render('technicien/planning.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    // Route pour traiter une intervention
    #[Route('/traiter-intervention/{id}', name: 'traiter_intervention', methods: ['GET', 'POST'])]
    public function traiterIntervention($id, Request $request): Response
    {
        // Récupérer l'intervention par son ID
        $intervention = $this->entityManager->getRepository(Intervention::class)->find($id);

        if (!$intervention) {
            throw $this->createNotFoundException('Intervention introuvable.');
        }

        if ($request->isMethod('POST')) {
            $analyse = $request->request->get('analyse');
            $etat = $request->request->get('etat');
            $confirm = $request->request->get('confirm');

            if ($confirm !== 'yes') {
                return $this->render('technicien/confirm_intervention.html.twig', [
                    'reclamation' => $intervention,
                    'analyse' => $analyse,
                    'etat' => $etat,
                ]);
            }

            // Traiter l'intervention
            $intervention->setAnalyse($analyse);
            $intervention->setEtat($etat);
            $intervention->setDate(new \DateTime());

            $this->entityManager->flush();

            return $this->redirectToRoute('technicien_planning');
        }

        return $this->render('technicien/traiter_intervention.html.twig', [
            'reclamation' => $intervention,
        ]);
    }
}
