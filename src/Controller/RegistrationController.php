<?php
namespace App\Controller;

use App\Entity\Client;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur pour la gestion de l'enregistrement des clients.
 */
class RegistrationController extends AbstractController
{
    // Enregistrement d'un nouveau client
    #[Route('/register-Client', name: 'app_register_Client')]
    public function registerClient(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $client = new Client();
        $form = $this->createForm(RegistrationFormType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération du mot de passe en clair
            $plainPassword = $form->get('plainPassword')->getData();

            // Encodage du mot de passe
            $client->setPassword($userPasswordHasher->hashPassword($client, $plainPassword));

            // Assign the ROLE_CLIENT to the new client
            $client->setRoles(['ROLE_CLIENT']);

            $entityManager->persist($client);
            $entityManager->flush();

            // Redirection après l'enregistrement réussi
            return $this->redirectToRoute('app_reclamation_index');
        }

        return $this->render('registration/registerClient.html.twig', [
            'registrationClientForm' => $form->createView(),
        ]);
    }
}
