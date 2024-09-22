<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Intervention;
use App\Repository\TechnicienRepository;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Contrôleur pour la gestion des réclamations en attente par les chefs d'équipe.
 */
#[Route('/attente')]
class AttenteController extends AbstractController
{
    private $csrfTokenManager;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    // Liste les réclamations en attente
    #[Route('/', name: 'attenteapp_attente_index', methods: ['GET'])]
    public function pending(ReclamationRepository $reclamationRepository): Response
    {
        $user = $this->getUser();

        if (!$user || $user instanceof \App\Entity\Client || $user instanceof \App\Entity\Technicien) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        // Récupère les réclamations en attente triées par email client
        $reclamations = $reclamationRepository->findPendingReclamationsSortedByClient();

        return $this->render('attente/attente.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    // Affiche la confirmation pour accepter une réclamation
    #[Route('/accept/{id}/confirm', name: 'attenteapp_attente_accept_confirm', methods: ['GET'])]
    public function confirmAccept(Reclamation $reclamation): Response
    {
        return $this->render('attente/accept.html.twig', [
            'reclamation' => $reclamation,
            'csrf_token' => $this->csrfTokenManager->getToken('accept' . $reclamation->getId()),
        ]);
    }

    // Accepte une réclamation
    #[Route('/accept/{id}', name: 'attenteapp_attente_accept', methods: ['POST'])]
    public function accept(Reclamation $reclamation, Request $request, EntityManagerInterface $entityManager): RedirectResponse
    {
        $reclamation->setEtatValidation('Acceptée');
        $entityManager->persist($reclamation);
        $entityManager->flush();

        $this->addFlash('success', 'Réclamation acceptée avec succès.');

        // Redirige vers la page d'assignation de technicien
        return $this->redirectToRoute('attenteapp_attente_assign_technician', ['id' => $reclamation->getId()]);
    }

    // Affiche la confirmation pour rejeter une réclamation
    #[Route('/reject/{id}/confirm', name: 'attenteapp_attente_reject_confirm', methods: ['GET'])]
    public function confirmReject(Reclamation $reclamation): Response
    {
        return $this->render('attente/reject.html.twig', [
            'reclamation' => $reclamation,
            'csrf_token' => $this->csrfTokenManager->getToken('reject' . $reclamation->getId()),
        ]);
    }

    // Rejette une réclamation
    #[Route('/reject/{id}', name: 'attenteapp_attente_reject', methods: ['POST'])]
    public function reject(Reclamation $reclamation, Request $request, EntityManagerInterface $entityManager): RedirectResponse
    {
        $reclamation->setEtatValidation('Refusée');
        $entityManager->persist($reclamation);
        $entityManager->flush();

        $this->addFlash('success', 'Réclamation refusée avec succès.');

        return $this->redirectToRoute('attenteapp_attente_index');
    }

 // Assigne un technicien à une réclamation et la transforme en intervention
#[Route('/assign-technician/{id}', name: 'attenteapp_attente_assign_technician', methods: ['GET', 'POST'])]
public function assignTechnician(Reclamation $reclamation, Request $request, EntityManagerInterface $entityManager, TechnicienRepository $technicienRepository): Response
{
    // Récupérer tous les techniciens disponibles
    $technicians = $technicienRepository->findAll();

    if ($request->isMethod('POST')) {
        $technicianId = $request->request->get('technician_id');
        $technician = $technicienRepository->find($technicianId);

        if ($technician) {
            // Assigner le technicien à la réclamation
            $reclamation->addAffectationTechnicien($technician);

            // Créer une nouvelle intervention si la réclamation n'est pas déjà une intervention
            if (!$reclamation instanceof Intervention) {
                $intervention = new Intervention();
                
                // Transférer les propriétés de la réclamation à l'intervention
                $intervention->setType($reclamation->getType());
                $intervention->setDescription($reclamation->getDescription());
                $intervention->setDateSoumission($reclamation->getDateSoumission());
                $intervention->setAdresse($reclamation->getAdresse());
                $intervention->setClientmail($reclamation->getClientmail());
                $intervention->setEtatValidation('acceptée');
                $intervention->addAffectationTechnicien($technician);

                // Fournir une valeur par défaut pour l'analyse
                $intervention->setAnalyse('Aucune analyse effectuée pour l\'instant'); // Valeur par défaut

                // Persister l'intervention
                $entityManager->persist($intervention);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Technicien assigné avec succès et intervention créée.');

            return $this->redirectToRoute('attenteapp_attente_index');
        }

        $this->addFlash('error', 'Technicien non trouvé.');
    }

    return $this->render('attente/assign_technician.html.twig', [
        'reclamation' => $reclamation,
        'technicians' => $technicians,
    ]);
}
}