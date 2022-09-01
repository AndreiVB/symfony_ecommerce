<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\Header;
use App\Entity\Carrier;
use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{

    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }
    
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $url = $this->adminUrlGenerator
        ->setController(OrderCrudController::class)
        ->setAction(Action::INDEX)
        ->generateUrl();

        return $this->redirect($url);      
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('The Shop Project');
    }

    public function configureMenuItems(): iterable
    {
        return [
        yield MenuItem::linkToRoute('Back to account', 'fa fa-arrow-left', 'app_account'),
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home'),
        yield MenuItem::linkToCrud('Users', 'fa fa-user', User::class),
        yield MenuItem::linkToCrud('Categories', 'fa fa-list', Category::class),
        yield MenuItem::linkToCrud('Products', 'fa fa-tag', Product::class),
        yield MenuItem::linkToCrud('Carriers', 'fa fa-truck', Carrier::class),
        yield MenuItem::linkToCrud('Orders', 'fa fa-shopping-cart', Order::class),
        yield MenuItem::linkToCrud('Headers', 'fa fa-desktop', Header::class),
        ];
    }
}