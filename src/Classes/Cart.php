<?php

namespace App\Classes;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart 
{
    private $session;

    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session)
    {
        $this->entityManager = $entityManager;
        $this->session = $session;
    }

    public function add($id)
    {
        $cart = $this->session->get('cart', []);

        if(!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $this->session->set('cart', $cart);           
    }

    public function get()
    {
        return $this->session->get('cart');
    }
    
    public function remove()
    {
        return $this->session->remove('cart');
    }

    public function delete($id)
    {
        $cart = $this->session->get('cart', []);

        unset($cart[$id]);

        return $this->session->set('cart', $cart); 
    }
    
    public function decrease($id)
    {
         $cart = $this->session->get('cart', []);

         if($cart[$id] > 1) {
            $cart[$id]--;
         } else {
            unset($cart[$id]);
         }

        return $this->session->set('cart', $cart); 
    }

    public function getFullCart() {
        $cartComplete = [];
        
        if($this->get()) {
                foreach($this->get() as $id => $quantity) {
                $productObject = $this->entityManager->getRepository(Product::class)->findOneById($id);   
                if(!$productObject) {
                    $this->delete($id);
                    continue;
                }
                $cartComplete[] = [
                    'product' => $productObject,
                    'quantity' => $quantity
                ];
            }
        }       
        
        return $cartComplete;
    }
}