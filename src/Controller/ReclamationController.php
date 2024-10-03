<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Contrôleur pour la gestion des réclamations.
 */
#[Route('/reclamation')]
final class ReclamationController extends AbstractController
{
   // Affiche la liste des réclamations pour le client connecté avec pagination et recherche
#[Route(name: 'app_reclamation_index', methods: ['GET'])]
public function index(ReclamationRepository $reclamationRepository, PaginatorInterface $paginator, Request $request): Response
{
    $client = $this->getUser();
    $reclamations = [];

    if ($client) {
        $clientMail = $client->getUsername();

        // Get the search query from the request
        $search = $request->query->get('search');

        // If there's a search query, filter results
        if ($search) {
            $query = $reclamationRepository->searchByClientMailAndQuery($clientMail, $search);
        } else {
            // Get all reclamations for the client if no search term is provided
            $query = $reclamationRepository->findByClientMail($clientMail);
        }

        // Paginate the results
        $reclamations = $paginator->paginate(
            $query, // query, not result
            $request->query->getInt('page', 1), // page number
           // 10 // limit per page
        );
    }

    return $this->render('reclamation/index.html.twig', [
        'reclamations' => $reclamations,
    ]);
}


    // Crée une nouvelle réclamation
    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set the client's email
            $client = $this->getUser();
            if ($client) {
                $clientMail = $client->getUsername();
                $reclamation->setClientmail($clientMail);
                $reclamation->setNomClient($client->getNom());
                $reclamation->setPrenomClient($client->getPrenom());
                $reclamation->setTelephoneClient($client->getTelephone());

                // Persist the new Reclamation entity in the database
                $entityManager->persist($reclamation);
                $entityManager->flush();

                // Redirect to the list of reclamations after successful submission
                return $this->redirectToRoute('app_reclamation_index');
            } else {
                // If user is not logged in, handle this case (optional)
                $this->addFlash('error', 'Vous devez être connecté pour soumettre une réclamation.');
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }

    // Affiche une réclamation spécifique
    #[Route('/{id}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    // Édite une réclamation existante
    #[Route('/{id}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reclamation_index');
        }

        return $this->render('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    // Supprime une réclamation
    #[Route('/{id}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reclamation->getId(), $request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reclamation_index');
    }
}
