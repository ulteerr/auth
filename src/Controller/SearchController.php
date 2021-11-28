<?php

namespace App\Controller;

use App\Repository\BooksRepository;
use App\Repository\CinemaRepository;
use App\Repository\GamesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class SearchController extends AbstractController
{
    #[Route('/search', name: 'blog_search')]
    public function search(
        \Symfony\Component\HttpFoundation\Request $request,
        CinemaRepository $cinemaRepository,
        BooksRepository $booksRepository,
        GamesRepository $gamesRepository
    )
    {
        $query = $request->query->get('q');

        $cinema = $cinemaRepository->searchByQuery($query);
        $books = $booksRepository->searchByQuery($query);
        $games = $gamesRepository->searchByQuery($query);

        return $this->render('search/index.html.twig', [
            'cinema' => $cinema,
            'books' => $books,
            'games' => $games,
        ]);
    }
}
