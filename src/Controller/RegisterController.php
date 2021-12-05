<?php
declare(strict_types=1);

namespace App\Controller;


use App\Security\OAuthGoogleAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegisterController extends AbstractController
{

    #[Route('/register', name: 'register')]
    public function register(
        UserPasswordEncoderInterface $passwordEncoder,
        Request                      $request,
        GuardAuthenticatorHandler    $guardHandler,
        OAuthGoogleAuthenticator     $authenticator,
    )
    {
        $user = $this->getUser();
        if ($user) {
            return $this->redirectToRoute('profile', ['id' => $this->getUser()->getId()]);
        } else {
            $user = new User (null, 'null', 'null');
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);


            if ($form->isSubmitted() && $form->isValid()) {
                $user = $form->getData();

                $password = $passwordEncoder->encodePassword(
                    $user,
                    $user->getPlainPassword()
                );
                $user->setPassword($password);

                $em = $this->getDoctrine()->getManager();

                $em->persist($user);
                $em->flush();
                return $guardHandler->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $authenticator,
                    'main'
                );
            }

            return $this->render('register/index.html.twig', [
                'form' => $form->createView(),

            ]);
        }
    }
}
