<?php

namespace App\Controller;

use DateTime;
use App\Classes\Cart;
use App\Entity\Order;
use App\Entity\Product;
use App\Form\OrderType;
use App\Entity\OrderDetails;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/order", name="app_order")
     */
    public function index(Cart $cart, Request $request): Response
    {

        if(!$this->getUser()->getAddresses()->getValues()) {
            return $this->redirectToRoute('app_account_address_add');
        }
        
        $form =$this->createForm(OrderType::class, null, [
            'user' =>$this->getUser()   
        ]);

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getFullCart()
        ]);
    }

    /**
     * @Route("/order/summary", name="app_order_summary", methods={"POST"})
     */
    public function add(Cart $cart, Request $request): Response
    {
        
        $form =$this->createForm(OrderType::class, null, [
            'user' =>$this->getUser()   
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $date = new DateTime();
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();
            $delivery_content = $delivery->getFirstName() . ' ' . $delivery->getLastName();
            $delivery_content .= '<br/>' . $delivery->getPhone();
            if($delivery->getCompany()) {
                $delivery_content .= '<br/>' . $delivery->getCompany();
            }
            $delivery_content .= '<br/>' . $delivery->getAddress();
            $delivery_content .= '<br/>' . $delivery->getPostalCode() . ' ' . $delivery->getCity();
            $delivery_content .= '<br/>' . $delivery->getCountry();
            
            //register order Order()
            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrice());
            $order->setDelivery($delivery_content);
            $order->setIsPaid(0);

            $this->entityManager->persist($order);
                                   
            //register products OrderDetails()          
            foreach($cart->getFullCart() as $product) {
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrice());
                $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);
                
                $this->entityManager->persist($orderDetails);
            }

            $this->entityManager->flush();

            return $this->render('order/add.html.twig', [
            'cart' => $cart->getFullCart(),
            'carrier' => $carriers,
            'delivery' => $delivery_content
        ]);
        }

        return $this->redirectToRoute('app_cart');
    }
}