<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

class SecurityController extends AbstractController
{
    /**
     * @var Environment $twig
     */
    private $twig;

    public function __construct(
        Environment $twig
    )
    {
        $this->twig = $twig;
    }

    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $user = $this->getUser();
        if ($user) {
            return $this->redirectToRoute('profile', ['id' => $this->getUser()->getId()]);
        } else {
            $lastUsername = $authenticationUtils->getLastUsername();
            $error = $authenticationUtils->getLastAuthenticationError();
            return new Response($this->twig->render('security/login.html.twig', [
                'last_username' => $lastUsername,
                'error' => $error
            ])
            );
        }
    }

    #[Route('/logout', name: 'logout')]
    public function logout()
    {
    }
}
