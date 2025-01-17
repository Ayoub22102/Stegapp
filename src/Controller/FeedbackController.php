<?php

namespace App\Controller;

use App\Entity\Feedback;
use App\Entity\Reclamation;
use App\Form\FeedbackType;
use App\Repository\FeedbackRepository;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for handling client feedback on interventions.
 */
#[Route('/feedback', name: 'feedback_')]
class FeedbackController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Displays a list of reclamations with interventions in the "Clôturé" state for the connected client.
     */
    #[Route('/list', name: 'list', methods: ['GET'])]
    public function list(ReclamationRepository $reclamationRepository, FeedbackRepository $feedbackRepository): Response
    {
        $client = $this->getUser();
        if (!$client) {
            throw $this->createAccessDeniedException('Veuillez vous connecter pour accéder à cette page.');
        }

        // Get reclamations with "Clôturé" interventions for the client
        $reclamationsWithCloturedInterventions = $reclamationRepository->findByClientAndCloturedInterventions($client->getUsername());

        // Filter out reclamations that already have feedback
        $reclamationsWithoutFeedback = array_filter($reclamationsWithCloturedInterventions, function ($intervention) use ($feedbackRepository) {
            return !$feedbackRepository->findOneBy(['reclamation' => $intervention]); 
        });

        return $this->render('feedback/list.html.twig', [
            'reclamations' => $reclamationsWithoutFeedback,
        ]);
    }

    /**
     * Submit feedback for a specific intervention (which is also a reclamation).
     */
    #[Route('/submit/{id}', name: 'submit', methods: ['GET', 'POST'])]
    public function submitFeedback($id, Request $request, FeedbackRepository $feedbackRepository): Response
    {
        // Find the reclamation by ID
        $reclamation = $this->entityManager->getRepository(Reclamation::class)->find($id);
        if (!$reclamation) {
            throw $this->createNotFoundException('Réclamation introuvable.');
        }

        // Ensure the current user is the owner of the reclamation
        $client = $this->getUser();
        if ($client->getUsername() !== $reclamation->getClientmail()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à donner un feedback pour cette réclamation.');
        }

        // Check if feedback already exists for this reclamation
        if ($feedbackRepository->findOneBy(['reclamation' => $reclamation])) {
            $this->addFlash('error', 'Vous avez déjà donné un feedback pour cette réclamation.');
            return $this->redirectToRoute('feedback_list');
        }

        // Create the feedback form
        $feedback = new Feedback();
        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set the linked reclamation to the feedback
            $feedback->setReclamation($reclamation);
            $this->entityManager->persist($feedback);
            $this->entityManager->flush();

            $this->addFlash('success', 'Merci pour votre feedback.');
            return $this->redirectToRoute('feedback_list');
        }

        return $this->render('feedback/submit.html.twig', [
            'form' => $form->createView(),
            'reclamation' => $reclamation,
        ]);
    }

    /**
     * Displays all feedback for the Chef d'équipe.
     */
    #[Route('/all', name: 'all', methods: ['GET'])]
    public function listAllFeedback(FeedbackRepository $feedbackRepository): Response
    {
        // Ensure the current user has the "ROLE_CHEF_EQUIPE"
        if (!$this->isGranted('ROLE_CHEF_EQUIPE')) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        // Fetch all feedback from the repository
        $feedbacks = $feedbackRepository->findAll();

        return $this->render('feedback/all.html.twig', [
            'feedbacks' => $feedbacks,
        ]);
    }

    #[Route('/details/{id}', name: 'feedback_details', methods: ['GET'])]
    public function feedbackDetails($id, FeedbackRepository $feedbackRepository): Response
    {
        // Retrieve the feedback by its ID
        $feedback = $feedbackRepository->find($id);

        if (!$feedback) {
            throw $this->createNotFoundException('Feedback introuvable.');
        }

        return $this->render('feedback/details.html.twig', [
            'feedback' => $feedback,
        ]);
    }
}
