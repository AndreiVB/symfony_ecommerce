<?php

namespace App\Controller\Admin;

use App\Classes\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;

class OrderCrudController extends AbstractCrudController
{
    private $entityManager;
    private $adminUrlGenerator;
    public function __construct(EntityManagerInterface $entityManager, AdminUrlGenerator $adminUrlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $updatePreparation = Action::new('updatePreparation', 'In preparation', 'fas fa-box-open' )->linkToCrudAction('updatePreparation');
        $updateDelivery = Action::new('updateDelivery', 'Delivery in progress', 'fas fa-truck')->linkToCrudAction('updateDelivery');

        return $actions
            ->add('detail', $updatePreparation)
            ->add('detail', $updateDelivery)
            ->add('index', 'detail');
    }

    public function updatePreparation(AdminContext $context)
    {
              
        $order = $context->getEntity()->getInstance();
        $order->setState(2);
        $this->entityManager->flush();

        $this->addFlash('succes', '<span class="badge rounded-pill bg-success"><strong>The order' . $order->getReference() . ' is in preparation</strong></span>');
    
        $url = $this->adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

        $mail = new Mail();
        $confirmationMessage = "Your order on The Shop is beeing prepared";
        $content = "Hello " . $order->getUser()->getFirstname() . ", your order on The Shop is beeing prepared";
        $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), $confirmationMessage, $content);
        
        return $this->redirect($url);      
        
    }


    public function updateDelivery(AdminContext $context)
    {
              
        $order = $context->getEntity()->getInstance();
        $order->setState(3);
        $this->entityManager->flush();

        $this->addFlash('notification', '<span class="badge rounded-pill bg-success"><strong>The order' . $order->getReference() . ' is beeing delivered</strong></span>');
    
        $url = $this->adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

        $mail = new Mail();
        $confirmationMessage = "Your order on The Shop is beeing delivered towards you";
        $content = "Hello " . $order->getUser()->getFirstname() . ", your order on The Shop is beeing delivered towards you";
        $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), $confirmationMessage, $content);
            
        return $this->redirect($url);      
        
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id' => 'DESC']);
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            DateField::new('createdAt', 'Ordered at'),
            TextField::new('user.fullName', 'User'),
            TextField::new('delivery', 'Delivery address')->onlyOnDetail()->renderAsHtml(),
            MoneyField::new('total', 'Total cart')->setCurrency('EUR'),
            TextField::new('carrierName', 'Carrier'),
            MoneyField::new('carrierPrice', 'Delivery taxes')->setCurrency('EUR'),
            ChoiceField::new('state')->setChoices([
                'Not paid' => 0,
                'Paid' => 1, 
                'In preparation' => 2,
                'Delivery in progress' => 3
                
            ]),
            ArrayField::new('orderDetails', 'Products bought')->hideOnIndex()->hideOnForm()
        ];
    }
    
}