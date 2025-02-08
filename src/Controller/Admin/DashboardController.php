<?php

namespace App\Controller\Admin;

use App\Entity\City;
use App\Entity\Community;
use App\Entity\Federation;
use App\Entity\InscriptionPayment;
use App\Entity\Member;
use App\Entity\MemberCategory;
use App\Entity\Mode;
use App\Entity\Position;
use App\Entity\Province;
use App\Entity\Sector;
use App\Entity\Territory;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {

//        return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // 1.1) If you have enabled the "pretty URLs" feature:
        // return $this->redirectToRoute('admin_user_index');
        //
        // 1.2) Same example but using the "ugly URLs" that were used in previous EasyAdmin versions:
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
         return $this->render('/admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Areprdc');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fas fa-tachometer-alt');

        yield MenuItem::section("Gestion des Membres");
        yield MenuItem::linkToCrud('Membre', 'fas fa-users', Member::class);

        yield MenuItem::section("Gestion Caisse");
        yield MenuItem::linkToCrud('Mode', 'fas fa-users', Mode::class);
        yield MenuItem::linkToCrud('Frais inscription', 'fas fa-users', InscriptionPayment::class);

        yield MenuItem::section("Configuration Membres");
        yield MenuItem::linkToCrud('Catégorie Membre', 'fas fa-id-card', MemberCategory::class);
        yield MenuItem::linkToCrud('Fonction', 'fas fa-briefcase', Position::class);
        yield MenuItem::linkToCrud('Fédération', 'fas fa-building', Federation::class);

        yield MenuItem::section("Configuration Lieu");
        yield MenuItem::linkToCrud('Province', 'fas fa-map-marked-alt', Province::class);
        yield MenuItem::linkToCrud('Territoire', 'fas fa-map', Territory::class);
        yield MenuItem::linkToCrud('Secteur', 'fas fa-map-signs', Sector::class);
        yield MenuItem::linkToCrud('Groupement', 'fas fa-sitemap', Community::class);
        yield MenuItem::linkToCrud('Ville', 'fas fa-sitemap', City::class);

    }
}
