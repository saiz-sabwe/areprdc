<?php

namespace App\Controller\Admin;

use App\Entity\City;
use App\Entity\Community;
use App\Entity\Currency;
use App\Entity\Federation;
use App\Entity\InscriptionPayment;
use App\Entity\Member;
use App\Entity\MemberCategory;
use App\Entity\Mode;
use App\Entity\Position;
use App\Entity\Province;
use App\Entity\Sector;
use App\Entity\Territory;
use App\Entity\User;
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
        return $this->render('/admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Areprdc');
    }

    public function configureMenuItems(): iterable
    {
        // Accessible à tout utilisateur authentifié
        yield MenuItem::linkToDashboard('Dashboard', 'fas fa-tachometer-alt');

        // ============================
        // Gestion des Membres
        // ============================
        yield MenuItem::section("Gestion des Membres");

        // Le menu "Membre" doit être visible pour tous les rôles autorisés à consulter la liste :
        // SECRETAIRE GENERAL, COMPTABLE, CHEF FINANCE, IMPLANTATION DU PARTI,
        // SERVICE D’ADHESION, SERVICE CLIENT ainsi que l’ADMIN.
        if (
            $this->isGranted('ROLE_ADMIN') ||
            $this->isGranted('ROLE_SECRETAIRE_GENERAL') ||
            $this->isGranted('ROLE_COMPTABLE') ||
            $this->isGranted('ROLE_CHEF_FINANCE') ||
            $this->isGranted('ROLE_IMPLANTATION_PARTI') ||
            $this->isGranted('ROLE_SERVICE_ADHESION') ||
            $this->isGranted('ROLE_SERVICE_CLIENT')
        ) {
            yield MenuItem::linkToCrud('Membre', 'fas fa-users', Member::class);
        }

        // "Utilisateur" (comptes admin) accessible uniquement au COMPTE ADMIN
        yield MenuItem::linkToCrud('Utilisateur', 'fas fa-user-cog', User::class);

        // ============================
        // Gestion Caisse
        // ============================
        yield MenuItem::section("Gestion Caisse");

        // "Mode" et "Devise" : accessibles aux utilisateurs du SERVICE D’ADHESION (et supérieurs)
        yield MenuItem::linkToCrud('Mode', 'fas fa-cogs', Mode::class)
            ->setPermission('ROLE_SERVICE_ADHESION');
        yield MenuItem::linkToCrud('Devise', 'fas fa-money-bill-wave', Currency::class)
            ->setPermission('ROLE_SERVICE_ADHESION');

        // "Frais inscription" : accessible pour SERVICE CLIENT et tous les autres rôles autorisés à consulter
        if (
            $this->isGranted('ROLE_SERVICE_CLIENT') ||
            $this->isGranted('ROLE_SERVICE_ADHESION') ||
            $this->isGranted('ROLE_IMPLANTATION_PARTI') ||
            $this->isGranted('ROLE_COMPTABLE') ||
            $this->isGranted('ROLE_CHEF_FINANCE') ||
            $this->isGranted('ROLE_SECRETAIRE_GENERAL') ||
            $this->isGranted('ROLE_ADMIN')
        ) {
            yield MenuItem::linkToCrud('Frais inscription', 'fas fa-money-check', InscriptionPayment::class);
        }

        // ============================
        // Configuration Membres
        // ============================
        // Ces options sont réservées aux rôles dont la permission est "voir seulement"
        // : SECRETAIRE GENERAL, COMPTABLE, CHEF FINANCE.
        if (
            $this->isGranted('ROLE_SECRETAIRE_GENERAL') ||
            $this->isGranted('ROLE_COMPTABLE') ||
            $this->isGranted('ROLE_CHEF_FINANCE')
        ) {
            yield MenuItem::section("Configuration Membres");
            yield MenuItem::linkToCrud('Catégorie Membre', 'fas fa-id-card', MemberCategory::class);
            yield MenuItem::linkToCrud('Fonction', 'fas fa-briefcase', Position::class);
            yield MenuItem::linkToCrud('Fédération', 'fas fa-building', Federation::class);
        }

        // ============================
        // Configuration Lieu
        // ============================
        // Restreint aux comptes ADMIN
        yield MenuItem::section("Configuration Lieu") ->setPermission('ROLE_ADMIN');;
        yield MenuItem::linkToCrud('Province', 'fas fa-map-marked-alt', Province::class)
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Territoire', 'fas fa-map', Territory::class)
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Secteur', 'fas fa-map-signs', Sector::class)
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Groupement', 'fas fa-sitemap', Community::class)
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Ville', 'fas fa-city', City::class)
            ->setPermission('ROLE_ADMIN');
    }
}
