<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\BooksRepository;
use App\Repository\CinemaRepository;
use App\Repository\GamesRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AdminController extends AbstractController
{
    public function __construct(
        CinemaRepository $cinemaRepository,
        BooksRepository  $booksRepository,
        GamesRepository  $gamesRepository
    )
    {
        $this->cinemaRepository = $cinemaRepository;
        $this->booksRepository = $booksRepository;
        $this->gamesRepository = $gamesRepository;
    }
    #[Route('/admin', name: 'admin')]
    public function index(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $userRepository->findAll();
        return $this->render('admin/index.html.twig', [
            'users' => $user,
        ]);
    }

    #[Route('/profileuser/{id}', name: 'profileuser')]
    public function profileUsers(User $userid)
    {

        $cinema = $this->cinemaRepository->findBy(array('user' => $userid), array('created_at' => 'desc'));
        $books = $this->booksRepository->findBy(array('user' => $userid), array('created_at' => 'desc'));
        $games = $this->gamesRepository->findBy(array('user' => $userid), array('created_at' => 'desc'));
        return $this->render('admin/profile.html.twig', [
            'user' => $userid,
            'films' => $cinema,
            'books' => $books,
            'games' => $games
        ]);

    }
}
