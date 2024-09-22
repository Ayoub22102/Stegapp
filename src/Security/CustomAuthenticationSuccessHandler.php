<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class CustomAuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private RouterInterface $router;
    private AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(RouterInterface $router, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->router = $router;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): ?Response
    {
        /** @var UserInterface $user */
        $user = $token->getUser();

        // Redirect based on roles
        if ($this->authorizationChecker->isGranted('ROLE_CLIENT')) {
            // Redirect client
            $url = $this->router->generate('app_reclamation_index');
        } elseif ($this->authorizationChecker->isGranted('ROLE_CHEF_EQUIPE')) {
            // Redirect chef d'équipe
            $url = $this->router->generate('attenteapp_attente_index');
        } elseif ($this->authorizationChecker->isGranted('ROLE_TECHNICIEN')) {
            // Redirect technicien
            $url = $this->router->generate('technicien_planning');
        } else {
            // Default redirect for any other role (admin or undefined)
            $url = $this->router->generate('admin_dashboard');
        }

        return new RedirectResponse($url);
    }
}
