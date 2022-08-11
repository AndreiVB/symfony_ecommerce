<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderSuccessController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/order/thank-you/{stripeSessionId}", name="app_order_validate")
     */
    public function index(Cart $cart, $stripeSessionId): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);
        

        if(!$order) {
            return $this->redirectToRoute('app_home');
        }
        
        if(!$order->isIsPaid()) {
            //empty the cart session
            $cart->remove();
            //modify isPaid status to 1
            $order->setIsPaid(1);
            $this->entityManager->flush();
            //send confirmation mail
            $mail = new Mail();
            $confirmationMessage = "You order on The Shop Project has been made";
            $content = "Hello " . $order->getUser()->getFirstname() . ", thank you for your order";
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), $confirmationMessage, $content);
        }
        
        
        //show some user order infos
        return $this->render('order_success/index.html.twig', [
            'order' => $order
        ]);
    }
}