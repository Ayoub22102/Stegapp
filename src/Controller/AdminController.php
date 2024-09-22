<?php

namespace App\Controller;

use App\Entity\Technicien;
use App\Entity\Chefequipe;
use App\Form\TechnicienType;
use App\Form\ChefequipeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\Extension\Core\Type\SearchType;

/**
 * Contrôleur pour la gestion des techniciens et chefs d'équipe par l'administrateur.
 */
#[Route('/admin')]
class AdminController extends AbstractController
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    // Affiche le tableau de bord de l'administrateur
    #[Route('/', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    // Liste tous les techniciens avec une fonctionnalité de recherche
    #[Route('/technicien', name: 'admin_technicien_index')]
    public function indexTechnicien(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Création du formulaire de recherche
        $searchForm = $this->createFormBuilder()
            ->setMethod('GET')
            ->add('search', SearchType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Rechercher un technicien...',
                    'class' => 'form-control'
                ],
                'required' => false
            ])
            ->getForm();

        $searchForm->handleRequest($request);
        $searchTerm = $searchForm->get('search')->getData();

        // Si un terme de recherche est fourni, filtrer les résultats
        if ($searchForm->isSubmitted() && $searchTerm) {
            $techniciens = $entityManager->getRepository(Technicien::class)->createQueryBuilder('t')
                ->where('t.nom LIKE :searchTerm OR t.prenom LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%')
                ->getQuery()
                ->getResult();
        } else {
            // Récupère tous les techniciens si aucun terme de recherche n'est fourni
            $techniciens = $entityManager->getRepository(Technicien::class)->findAll();
        }

        return $this->render('admin/technicien_index.html.twig', [
            'techniciens' => $techniciens,
            'search_form' => $searchForm->createView(),
        ]);
    }

    // Crée un nouveau technicien
    #[Route('/technicien/new', name: 'admin_technicien_new')]
    public function newTechnicien(Request $request, EntityManagerInterface $entityManager): Response
    {
        $technicien = new Technicien();
        $form = $this->createForm(TechnicienType::class, $technicien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hachage du mot de passe
            $hashedPassword = $this->passwordHasher->hashPassword($technicien, $technicien->getPassword());
            $technicien->setPassword($hashedPassword);
            $technicien->setRoles(['ROLE_TECHNICIEN']);

            $entityManager->persist($technicien);
            $entityManager->flush();

            $this->addFlash('success', 'Le technicien a été créé avec succès.');

            return $this->redirectToRoute('admin_technicien_index');
        }

        return $this->render('admin/technicien_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Modifie un technicien existant
    #[Route('/technicien/{id}/edit', name: 'admin_technicien_edit')]
    public function editTechnicien(Request $request, Technicien $technicien, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TechnicienType::class, $technicien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mise à jour du mot de passe si modifié
            if ($form->get('password')->getData()) {
                $hashedPassword = $this->passwordHasher->hashPassword($technicien, $form->get('password')->getData());
                $technicien->setPassword($hashedPassword);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Le technicien a été modifié avec succès.');

            return $this->redirectToRoute('admin_technicien_index');
        }

        return $this->render('admin/technicien_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Supprime un technicien
    #[Route('/technicien/{id}/delete', name: 'admin_technicien_delete', methods: ['POST'])]
    public function deleteTechnicien(Request $request, Technicien $technicien, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $technicien->getId(), $request->request->get('_token'))) {
            $entityManager->remove($technicien);
            $entityManager->flush();

            $this->addFlash('success', 'Le technicien a été supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_technicien_index');
    }

    // Liste tous les chefs d'équipe avec une fonctionnalité de recherche
    #[Route('/chefequipe', name: 'admin_chefequipe_index')]
    public function indexChefequipe(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Création du formulaire de recherche
        $searchForm = $this->createFormBuilder()
            ->setMethod('GET')
            ->add('search', SearchType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Rechercher un chef d\'équipe...',
                    'class' => 'form-control'
                ],
                'required' => false
            ])
            ->getForm();

        $searchForm->handleRequest($request);
        $searchTerm = $searchForm->get('search')->getData();

        // Filtrer les résultats en fonction de la recherche
        if ($searchForm->isSubmitted() && $searchTerm) {
            $chefs = $entityManager->getRepository(Chefequipe::class)->createQueryBuilder('c')
                ->where('c.nom LIKE :searchTerm OR c.prenom LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%')
                ->getQuery()
                ->getResult();
        } else {
            // Récupère tous les chefs d'équipe
            $chefs = $entityManager->getRepository(Chefequipe::class)->findAll();
        }

        return $this->render('admin/chefequipe_index.html.twig', [
            'chefs' => $chefs,
            'search_form' => $searchForm->createView(),
        ]);
    }

    // Crée un nouveau chef d'équipe
    #[Route('/chefequipe/new', name: 'admin_chefequipe_new', methods: ['GET', 'POST'])]
    public function newChefequipe(Request $request, EntityManagerInterface $entityManager): Response
    {
        $chefequipe = new Chefequipe();
        $form = $this->createForm(ChefequipeType::class, $chefequipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $this->passwordHasher->hashPassword($chefequipe, $chefequipe->getPassword());
            $chefequipe->setPassword($hashedPassword);
            $chefequipe->setRoles(['ROLE_CHEF_EQUIPE']);

            $entityManager->persist($chefequipe);
            $entityManager->flush();

            $this->addFlash('success', 'Le chef d\'équipe a été créé avec succès.');

            return $this->redirectToRoute('admin_chefequipe_index');
        }

        return $this->render('admin/chefequipe_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Modifie un chef d'équipe existant
    #[Route('/chefequipe/{id}/edit', name: 'admin_chefequipe_edit')]
    public function editChefequipe(Request $request, Chefequipe $chefequipe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChefequipeType::class, $chefequipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mise à jour du mot de passe si modifié
            if ($form->get('password')->getData()) {
                $hashedPassword = $this->passwordHasher->hashPassword($chefequipe, $form->get('password')->getData());
                $chefequipe->setPassword($hashedPassword);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Le chef d\'équipe a été modifié avec succès.');

            return $this->redirectToRoute('admin_chefequipe_index');
        }

        return $this->render('admin/chefequipe_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Supprime un chef d'équipe
    #[Route('/chefequipe/{id}/delete', name: 'admin_chefequipe_delete', methods: ['POST'])]
    public function deleteChefequipe(Request $request, Chefequipe $chefequipe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $chefequipe->getId(), $request->request->get('_token'))) {
            $entityManager->remove($chefequipe);
            $entityManager->flush();

            $this->addFlash('success', 'Le chef d\'équipe a été supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_chefequipe_index');
    }
}
