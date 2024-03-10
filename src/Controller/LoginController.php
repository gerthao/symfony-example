<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    public function __construct(protected AuthenticationUtils $utils, protected LoggerInterface $logger)
    {
    }

    #[Route('/login', name: 'app_login')]
    public function index(): Response
    {
        $lastUsername = $this->utils->getLastUsername();
        $error        = $this->utils->getLastAuthenticationError();

        return $this->render('login/index.html.twig', [
            'lastUsername' => $lastUsername,
            'error'        => $error,
        ]);
    }

    /**
     * Symfony only cares that we have a route defined for logging out.
     * So we can leave the method block empty, and it will be handled
     * by the framework.  Go see security.yaml under the
     * "security.firewalls.main.logout".
     *
     * @return void
     *
     */
    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {
    }
}
