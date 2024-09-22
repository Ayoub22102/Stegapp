<?php

namespace App\Controller;

use App\Entity\Intervention;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for closing interventions.
 */
#[Route('/cloture', name: 'cloture_')]
class ClotureController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/interventions', name: 'interventions', methods: ['GET'])]
    public function showTerminéeInterventions(): Response
    {
        // Fetch all interventions with etat = 'terminée'
        $interventions = $this->entityManager->getRepository(Intervention::class)->findBy(['etat' => 'terminée']);

        return $this->render('cloture/interventions.html.twig', [
            'interventions' => $interventions,
        ]);
    }

    #[Route('/cloturer/{id}', name: 'cloturer', methods: ['GET', 'POST'])]
    public function cloturerIntervention($id, Request $request): Response
    {
        // Fetch the intervention by its ID
        $intervention = $this->entityManager->getRepository(Intervention::class)->find($id);

        if (!$intervention) {
            throw $this->createNotFoundException('Intervention introuvable.');
        }

        if ($request->isMethod('POST')) {
            $confirm = $request->request->get('confirm');

            if ($confirm !== 'yes') {
                // Display confirmation message
                return $this->render('cloture/confirm_cloture.html.twig', [
                    'intervention' => $intervention,
                ]);
            }

            // Update the intervention state to "Clôturé"
            $intervention->setEtat('Clôturé');
            $this->entityManager->flush();

            return $this->redirectToRoute('cloture_interventions');
        }

        return $this->render('cloture/cloturer_intervention.html.twig', [
            'intervention' => $intervention,
        ]);
    }
}
