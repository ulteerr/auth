<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\BooksRepository;
use App\Repository\CinemaRepository;
use App\Repository\GamesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
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

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $cinema = $this->cinemaRepository->findBy(array(), array('created_at' => 'desc'), 1);
        $books = $this->booksRepository->findBy(array(), array('created_at' => 'desc'), 1);
        $games = $this->gamesRepository->findBy(array(), array('created_at' => 'desc'), 1);
        return $this->render('home/index.html.twig', [
            'films' => $cinema,
            'books' => $books,
            'games' => $games,
        ]);
    }

    #[Route('/profile/{id}', name: 'profile')]
    public function dashboard(User $userid,\Symfony\Component\HttpFoundation\Request $request)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('login');
        }
        if ($userid->getId() !== $this->getUser()->getId()) {
            return $this->redirectToRoute('profile', [ 'id'=> $this->getUser()->getId()]);
        }
        $cinema = $this->cinemaRepository->findBy(array('user' => $user), array('created_at' => 'desc'));
        $books = $this->booksRepository->findBy(array('user' => $user), array('created_at' => 'desc'));
        $games = $this->gamesRepository->findBy(array('user' => $user), array('created_at' => 'desc'));
        return $this->render('home/profile.html.twig', [
            'user' => $user,
            'films' => $cinema,
            'books' => $books,
            'games' => $games
        ]);

    }
}