<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;


class OAuthController extends AbstractController
{

    /**
     * @param ClientRegistry $clientRegistry
     *
     * @return RedirectResponse
     *
     */
    #[Route('/connect/google', name: 'connect_google_start')]
    public function redirectToGoogleConnect(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect([
                'email', 'profile'
            ]);
    }

    /**
     *
     * @return JsonResponse|RedirectResponse
     */
    #[Route('/google/auth/callback', name: 'google_auth')]
    public function connectGoogleCheck()
    {
        if (!$this->getUser()) {
            return new JsonResponse(['status' => false, 'message' => "User not found!"]);
        } else {
            return $this->redirectToRoute('profile', [ 'id'=> $this->getUser()->getId()]);
        }
    }

    /**
     *
     * @param ClientRegistry $clientRegistry
     *
     * @return RedirectResponse
     */
    #[Route('/connect/github', name: 'connect_github_start')]
    public function redirectToGithubConnect(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('github')
            ->redirect([
                'user', 'public_repo'
            ]);
    }

    /**
     *
     * @return RedirectResponse|Response
     */
    #[Route('/github/auth/callback', name: 'github_auth')]
    public function authenticateGithubUser()
    {
        if (!$this->getUser()) {
            return new Response('User nof found', 404);
        }

        return $this->redirectToRoute('profile', [ 'id'=> $this->getUser()->getId()]);
    }
}
