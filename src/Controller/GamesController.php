<?php

namespace App\Controller;

use App\Entity\Books;
use App\Entity\Games;
use App\Form\BooksType;
use App\Repository\BooksRepository;
use App\Repository\GamesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Cocur\Slugify\Slugify;
use App\Form\CommentType;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\HttpFoundation\File\Exception\FileException;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\String\Slugger\SluggerInterface;

class GamesController extends AbstractController
{
    public function __construct(GamesRepository $gamesRepository)
    {
        $this->gamesRepository = $gamesRepository;
    }

    #[Route('/games', name: 'games')]
    public function index(): Response
    {
        $books = $this->gamesRepository->findBy(array(),array('created_at'=>'desc'));
        return $this->render('games/index.html.twig', [
            'games' => $books,
        ]);
    }

    #[Route('/games/new/', name: 'new_games')]
    public function addGames(
        \Symfony\Component\HttpFoundation\Request $request,
        Slugify                                   $slugify,
        SluggerInterface                          $slugger
    )
    {
        $user = $this->getUser();
        $games= new Games();
        $games->setUser($user);
        $form = $this->createForm(BooksType::class, $games);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $games->setSlug($slugify->slugify($games->getTitle()));
            $games->setCreatedAt(new \DateTime());
            /** @var UploadedFile $img */
            $img = $form->get('img')->getData();
            $newFilename = 'no_image.png';
            $games->setImg($newFilename);
            if ($img) {
                $originalFilename = pathinfo($img->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $img->guessExtension();
                try {
                    $img->move(
                        $this->getParameter('img_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    'Попробуйте повторить позже';
                }
                $games->setImg($newFilename);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($games);
            $em->flush();

            return $this->redirectToRoute('games_show',['slug' => $games->getSlug()]);
        }
        return $this->render('games/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/games/{slug}', name: 'games_show')]
    public function games(Games $games, \Symfony\Component\HttpFoundation\Request $request)
    {
        $user = $this->getUser();
        $comment = new \App\Entity\Comment();
        $comment->setUser($user);
        $games->addComment($comment);

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('games_show', ['slug' => $games->getSlug()]);
        }

        return $this->render('games/show.html.twig', [
            'games' => $games,
            'comment' => $form->createView()
        ]);
    }
    #[Route('/games/{slug}/edit', name: 'games_edit')]
    public function edit(Games $games, \Symfony\Component\HttpFoundation\Request $request, Slugify $slugify)
    {
        $form = $this->createForm(BooksType::class, $games);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $games->setSlug($slugify->slugify($games->getTitle()));
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('games_show', [
                'slug' => $games->getSlug()
            ]);
        }

        return $this->render('games/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/games/{slug}/delete', name: 'games_delete')]
    public function delete(Games $games)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($games);
        $em->flush();

        return $this->redirectToRoute('games');
    }
}
