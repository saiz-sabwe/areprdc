<?php

namespace App\Controller\Admin;

use App\Entity\Federation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FederationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Federation::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        // Si l'utilisateur connecté n'est pas admin,
        // on désactive la création, l'édition et la suppression
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $actions
                ->disable(Action::NEW, Action::EDIT, Action::DELETE)
                ->add(Crud::PAGE_INDEX, Action::DETAIL);
        }

        // Pour un compte admin, toutes les actions sont disponibles
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_NEW, Action::INDEX);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            AssociationField::new('city', 'Ville'),
            TextField::new('label'),
        ];
    }
}
