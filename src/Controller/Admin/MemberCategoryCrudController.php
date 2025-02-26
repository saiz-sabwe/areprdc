<?php

namespace App\Controller\Admin;

use App\Entity\MemberCategory;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MemberCategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MemberCategory::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        // Pour les utilisateurs NON-admin (Secrétaire général, Comptable, Chef Finance, etc.)
        // on désactive les actions de création, édition et suppression.
//        if (!$this->isGranted('ROLE_ADMIN')) {
//            return $actions
//                ->disable(Action::NEW, Action::EDIT, Action::DELETE)
//                ->add(Crud::PAGE_INDEX, Action::DETAIL);
//        }

        // Pour les comptes Admin, on autorise toutes les actions.
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_NEW, Action::INDEX);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('label'),
        ];
    }
}
