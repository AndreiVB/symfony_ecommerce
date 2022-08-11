<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderCancelController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/order/error/{stripeSessionId}", name="app_order_cancel")
     */
    public function index($stripeSessionId): Response
    {

        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        //another condition in if :  || $order->getUser != $this->getUser()
        if(!$order) {
            return $this->redirectToRoute('app_home');
        }
        
        //send error mail
        return $this->render('order_cancel/index.html.twig', [
            'order' => $order
        ]);
    }
}