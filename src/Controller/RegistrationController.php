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
class RegistrationController extends AbstractController
{
    #[Route('/register-Client', name: 'app_register_Client')]
    public function registerClient(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager,): Response
    {
        // Initialize the client entity
        $client = new Client();

        // Create the registration form
        $form = $this->createForm(RegistrationFormType::class, $client);
        $form->handleRequest($request);

        // Process the form submission if it is valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Get the plain password from the form data
            $plainPassword = $form->get('plainPassword')->getData();

            // Encode the plain password
            $client->setPassword($userPasswordHasher->hashPassword($client, $plainPassword));

            // Assign the ROLE_CLIENT to the new client
            $client->setRoles(['ROLE_CLIENT']);

            // Persist the client entity to the database
            $entityManager->persist($client);
            $entityManager->flush();

            // Redirect to the 'reclamation' page after successful registration
            return $this->redirectToRoute('app_reclamation_index');
        }

        // Render the registration form in the view
        return $this->render('registration/registerClient.html.twig', [
            'registrationClientForm' => $form->createView(),
        ]);
    }
}
