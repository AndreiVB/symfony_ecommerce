<?php

namespace App\Controller;

use App\Entity\User;
use App\Classes\Mail;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class RegisterController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/register", name="app_register")
     */
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_account');
        }

        //variable to notificate registration is valid
        $notification = null;

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            
            $user = $form->getData();
            
            $searchEmail = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());

            if(!$searchEmail) {
                $password = $passwordHasher->hashPassword($user, $user->getPassword());
                $user->setPassword($password);

                $this->entityManager->persist($user);
                $this->entityManager->flush();  
            
                $mail = new Mail();
                $welcomeMesssage = "Welcome on The Shop Project";
                $content = "Hello " . $user->getFirstname() . ", hope you will have a great time on our site. We have a great diversity of fashion products. Enjoy shopping right now!";
                $mail->send($user->getEmail(), $user->getFirstname(), $welcomeMesssage, $content);
                $notification = "You have registered successfully";

            } else {
                $notification = "Email address already taken";
            }  
            return $this->redirectToRoute('app_login');                      
        }
        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}