<?php

namespace App\Controller;

use App\Entity\Cinema;
use App\Form\CinemaType;
use App\Repository\CinemaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Cocur\Slugify\Slugify;
use App\Form\CommentType;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\HttpFoundation\File\Exception\FileException;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\String\Slugger\SluggerInterface;

class CinemaController extends AbstractController
{
    public function __construct(CinemaRepository $cinemaRepository)
    {
        $this->cinemaRepository = $cinemaRepository;
    }

    #[Route('/cinema', name: 'cinema')]
    public function index(): Response
    {
        $cinema = $this->cinemaRepository->findBy(array(),array('created_at'=>'desc'));
        return $this->render('cinema/index.html.twig', [
            'cinema' => $cinema,
        ]);
    }

    #[Route('/cinema/new/', name: 'new_cinema')]
    public function addCinema(
        \Symfony\Component\HttpFoundation\Request $request,
        Slugify                                   $slugify,
        SluggerInterface                          $slugger
    )
    {
        $user = $this->getUser();
        $cinema = new Cinema();
        $cinema->setUser($user);
        $form = $this->createForm(CinemaType::class, $cinema);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $cinema->setSlug($slugify->slugify($cinema->getTitle()));
            $cinema->setCreatedAt(new \DateTime());
            /** @var UploadedFile $img */
            $img = $form->get('img')->getData();
            $newFilename = 'no_image.png';
            $cinema->setImg($newFilename);
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
                $cinema->setImg($newFilename);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($cinema);
            $em->flush();

            return $this->redirectToRoute('cinema_show',['slug' => $cinema->getSlug()]);
        }
        return $this->render('cinema/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/cinema/{slug}', name: 'cinema_show')]
    public function cinema(Cinema $cinema, \Symfony\Component\HttpFoundation\Request $request)
    {
        $user = $this->getUser();
        $comment = new \App\Entity\Comment();
        $comment->setUser($user);
        $cinema->addComment($comment);

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('cinema_show', ['slug' => $cinema->getSlug()]);
        }

        return $this->render('cinema/show.html.twig', [
            'cinema' => $cinema,
            'comment' => $form->createView()
        ]);
    }
    #[Route('/cinema/{slug}/edit', name: 'cinema_edit')]
    public function edit(Cinema $cinema, \Symfony\Component\HttpFoundation\Request $request, Slugify $slugify)
    {
        $form = $this->createForm(CinemaType::class, $cinema);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cinema->setSlug($slugify->slugify($cinema->getTitle()));
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('cinema_show', [
                'slug' => $cinema->getSlug()
            ]);
        }

        return $this->render('cinema/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/cinema/{slug}/delete', name: 'cinema_delete')]
    public function delete(Cinema $cinema)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($cinema);
        $em->flush();

        return $this->redirectToRoute('cinema');
    }
}
