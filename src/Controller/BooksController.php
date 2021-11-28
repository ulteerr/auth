<?php

namespace App\Controller;

use App\Entity\Books;
use App\Form\BooksType;
use App\Repository\BooksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Cocur\Slugify\Slugify;
use App\Form\CommentType;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\HttpFoundation\File\Exception\FileException;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\String\Slugger\SluggerInterface;

class BooksController extends AbstractController
{
    public function __construct(BooksRepository $booksRepository)
    {
        $this->booksRepository = $booksRepository;
    }

    #[Route('/books', name: 'books')]
    public function index(): Response
    {
        $books = $this->booksRepository->findBy(array(),array('created_at'=>'desc'));
        return $this->render('books/index.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/books/new/', name: 'new_books')]
    public function addBooks(
        \Symfony\Component\HttpFoundation\Request $request,
        Slugify                                   $slugify,
        SluggerInterface                          $slugger
    )
    {
        $user = $this->getUser();
        $books = new Books();
        $books->setUser($user);
        $form = $this->createForm(BooksType::class, $books);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $books->setSlug($slugify->slugify($books->getTitle()));
            $books->setCreatedAt(new \DateTime());
            /** @var UploadedFile $img */
            $img = $form->get('img')->getData();
            $newFilename = 'no_image.png';
            $books->setImg($newFilename);
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
                $books->setImg($newFilename);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($books);
            $em->flush();

            return $this->redirectToRoute('books_show',['slug' => $books->getSlug()]);
        }
        return $this->render('books/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/books/{slug}', name: 'books_show')]
    public function books(Books $books, \Symfony\Component\HttpFoundation\Request $request)
    {
        $user = $this->getUser();
        $comment = new \App\Entity\Comment();
        $comment->setUser($user);
        $books->addComment($comment);

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('books_show', ['slug' => $books->getSlug()]);
        }

        return $this->render('books/show.html.twig', [
            'books' => $books,
            'comment' => $form->createView()
        ]);
    }
    #[Route('/books/{slug}/edit', name: 'books_edit')]
    public function edit(Books $books, \Symfony\Component\HttpFoundation\Request $request, Slugify $slugify)
    {
        $form = $this->createForm(BooksType::class, $books);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $books->setSlug($slugify->slugify($books->getTitle()));
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('books_show', [
                'slug' => $books->getSlug()
            ]);
        }

        return $this->render('books/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/books/{slug}/delete', name: 'books_delete')]
    public function delete(Books $books)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($books);
        $em->flush();

        return $this->redirectToRoute('books');
    }
}
