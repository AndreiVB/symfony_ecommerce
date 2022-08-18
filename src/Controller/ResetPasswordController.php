<?php

namespace App\Controller;

use App\Entity\User;
use App\Classes\Mail;
use App\Entity\ResetPassword;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordController extends AbstractController
{
     private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * @Route("/forgotten-password", name="app_reset_password")
     */
    public function index(Request $request): Response
    {
        if($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        if($request->get('email')) {
            $user = $this->entityManager->getRepository(User::class)->findOneByEmail($request->get('email'));
            if($user) {
                //save in db the password reset request
                $reset_password = new ResetPassword();
                $reset_password->setUser($user);
                $reset_password->setToken(uniqid());
                $reset_password->setCreatedAt(new \DateTime());
                $this->entityManager->persist($reset_password);
                $this->entityManager->flush();

                // send mail with reset link
                $mail = new Mail();
                $url = $this->generateUrl('app_update_password', [
                    'token' => $reset_password->getToken()
                ]);
                $confirmationMessage = 'Reset your password on The shop';
                $content = "Hello " . $user->getFirstname() . " You have requested a password reset. Please click on the link to <a href='".$url."'>reset your password</a>. The link is valid for the next 2 hours.";
                $mail->send($user->getEmail(), $user->getFirstname(). ' ' . $user->getLastname(), $confirmationMessage, $content);   
                $this->addFlash('notification', 'Your will receive a mail shortly with the link for password reset');           
            } else {
                $this->addFlash('notification', 'Wrong email address');
            }
        }
        return $this->render('reset_password/index.html.twig');
    }

    /**
     * @Route("/modify-your-password/{token}", name="app_update_password")
     */

    public function update(Request $request, $token, UserPasswordHasherInterface $hasher) 
    {
        $reset_password = $this->entityManager->getRepository(ResetPassword::class)->findOneByToken($token);
        if(!$reset_password) {
            return $this->redirectToRoute('app_reset_password');
        } else {
            $now = new \DateTime();
            //token expired
            if($now > $reset_password->getCreatedAt()->modify('+ 2 hour')) {
                $this->addFlash('notification', 'Your password request has expired. Please reset it again');
                return $this->redirectToRoute('app_reset_password');
            }
            $form = $this->createForm(ResetPasswordType::class);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                $new_password = $form->get('new_password')->getData();
                //password hash
                $password = $hasher->hashPassword($reset_password->getUser(), $new_password);
                $reset_password->getUser()->setPassword($password);

                $this->entityManager->flush(); 

                $this->addFlash('notification', 'Your have successfully changed your password');
                
                return $this->redirectToRoute('app_login');
            }
            return $this->render('reset_password/update.html.twig', [
                'form' => $form->createView()
            ]);
           //continue with password reset
    
        }
    }
}