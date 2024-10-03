<?php

namespace App\Controller;

use App\Entity\Intervention;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    #[Route('/cloturer/{id}', name: 'cloturer', methods: ['POST'])]
    public function cloturerIntervention($id): Response
    {
        // Fetch the intervention by its ID
        $intervention = $this->entityManager->getRepository(Intervention::class)->find($id);

        if (!$intervention) {
            throw $this->createNotFoundException('Intervention introuvable.');
        }

        // Update the intervention state to "Clôturé"
        $intervention->setEtat('Clôturé');

        // Update the corresponding reclamation's etat_validation to "Clôturée"
        $intervention->setEtatValidation('Clôturée');

        $this->entityManager->flush();

        // Redirect back to the list of interventions
        return $this->redirectToRoute('cloture_interventions');
    }
}
