<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountPasswordController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/account/change-password", name="app_account_password")
     */
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $infoUpdate = null;
        $user =$this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $old_password = $form->get('old_password')->getData();
            
            if($passwordHasher->isPasswordValid($user, $old_password)) {
                $new_password = $form->get('new_password')->getData();
                $password = $passwordHasher->hashPassword($user, $new_password);

                $user->setPassword($password);


                $this->entityManager->flush();  
                $infoUpdate = "Your password has been updated"; 
            } else {
                $infoUpdate = "Wrong current password";
            }
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
            'infoUpdate' => $infoUpdate
        ]);
    }
}