<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Classes\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Stripe\Checkout\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    /**
     * @Route("/order/create-session/{reference}", name="app_stripe_create_session")
     */
    public function index(EntityManagerInterface $entityManager, Cart $cart, $reference)
    {

        $products_for_stripe = [];
        $YOUR_DOMAIN = 'https://127.0.0.1:8000';

        $order = $entityManager->getRepository(Order::class)->findOneByReference($reference);

        if(!$order) {
            return $this->redirectToRoute('order'); 
            // return new JsonResponse(['error' => 'order']);          
        }

        foreach($order->getOrderDetails()->getValues() as $product) {
            $product_object = $entityManager->getRepository(Product::class)->findOneByName($product->getProduct());
            $products_for_stripe[] = [
                    'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product->getPrice(),
                    'product_data' => [
                        'name' => $product->getProduct(),
                        'images' => [$YOUR_DOMAIN."/uploads/".$product_object->getIllustration()],
                    ],                  
                ],
                'quantity' => $product->getQuantity(),
                ];
        }

        $products_for_stripe[] = [
                    'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $order->getCarrierPrice(),
                    'product_data' => [
                        'name' => $order->getCarrierName(),
                        'images' => [$YOUR_DOMAIN],
                    ],                  
                ],
                'quantity' => 1,
        ];

        Stripe::setApiKey('sk_test_51LQUKMFDr7nDVKwUDbbS5C1NywoHdWpum9Atn2GR9EBUyp2II8gNE8k1hV9LgRHKJdYg1MAjOXJ67Do4Ni4Tghip00vfbMlgqA');
            
            $checkout_session = Session::create([
            'customer_email' =>$this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [[
                $products_for_stripe               
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/order/thank-you/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/order/error/{CHECKOUT_SESSION_ID}',
            ]);

            $order->setStripeSessionId($checkout_session->id);
            $entityManager->flush();

            return $this->redirect($checkout_session->url);
            // $response = new JsonResponse(['id' => $checkout_session->id]);
            // return $response;
           
    }
}