<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\MemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    /**
     * Configuration des actions EasyAdmin (ajout du bouton DETAIL, etc.)
     */
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_NEW, Action::INDEX);
    }

    /**
     * Champs du formulaire EasyAdmin
     */
    public function configureFields(string $pageName): iterable
    {
        $allowedRoles = $this->getAllowedRoles();

        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('username', "Téléphone"),
            TextField::new('password', "Mot de passe")
                ->setFormType(PasswordType::class)
                ->onlyWhenCreating(),
            ChoiceField::new('roles', 'Roles')
                ->setChoices($allowedRoles)
                ->allowMultipleChoices(),
            AssociationField::new('partisan', 'Membre')
                ->setFormTypeOptions([
                    'query_builder' => function (MemberRepository $mr) {
                        // On affiche uniquement les membres qui ne sont pas déjà liés à un User
                        return $mr->findMembersNotLinked();
                    },
                ]),
        ];
    }

    /**
     * Renvoie la liste des rôles autorisés pour la création d’un nouvel utilisateur
     * en fonction du rôle du compte actuellement connecté.
     */
    private function getAllowedRoles(): array
    {
        $currentUser = $this->getUser();
        if (!$currentUser) {
            return [];
        }
        $roles = $currentUser->getRoles();

        if (in_array('ROLE_ADMIN', $roles, true)) {
            // L’admin peut créer des rôles <= ADMIN
            return [
                'Admin'                => 'ROLE_ADMIN',
                'Secrétaire Général'   => 'ROLE_SECRETAIRE_GENERAL',
                'Comptable'            => 'ROLE_COMPTABLE',
                'Chef Finance'         => 'ROLE_CHEF_FINANCE',
                'Service Adhésion'     => 'ROLE_SERVICE_ADHESION',
                'Implantation Parti'   => 'ROLE_IMPLANTATION_PARTI',
                'Utilisateur'          => 'ROLE_USER',
            ];
        } elseif (in_array('ROLE_SECRETAIRE_GENERAL', $roles, true)) {
            return [
                'Comptable'            => 'ROLE_COMPTABLE',
                'Chef Finance'         => 'ROLE_CHEF_FINANCE',
                'Service Adhésion'     => 'ROLE_SERVICE_ADHESION',
                'Implantation Parti'   => 'ROLE_IMPLANTATION_PARTI',
                'Utilisateur'          => 'ROLE_USER',
            ];
        } elseif (in_array('ROLE_COMPTABLE', $roles, true) || in_array('ROLE_CHEF_FINANCE', $roles, true)) {
            return [
                'Service Adhésion'     => 'ROLE_SERVICE_ADHESION',
                'Implantation Parti'   => 'ROLE_IMPLANTATION_PARTI',
                'Utilisateur'          => 'ROLE_USER',
            ];
        } elseif (
            in_array('ROLE_SERVICE_ADHESION', $roles, true)
            || in_array('ROLE_IMPLANTATION_PARTI', $roles, true)
            || in_array('ROLE_SERVICE_CLIENT', $roles, true)
        ) {
            return [
                'Utilisateur' => 'ROLE_USER',
            ];
        }

        return [];
    }

    /**
     * Méthode appelée lors de la création (persist) d’un nouvel utilisateur
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof User) {
            return;
        }

        // Hash du mot de passe si renseigné
        if ($entityInstance->getPassword()) {
            $hashedPassword = $this->passwordHasher->hashPassword(
                $entityInstance,
                $entityInstance->getPassword()
            );
            $entityInstance->setPassword($hashedPassword);
        }

        // Lier le membre (Member) à l’utilisateur, si un Member est sélectionné
        $member = $entityInstance->getPartisan();
        if ($member) {
            $member->setUser($entityInstance);
            $entityManager->persist($member);
        }

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    /**
     * Méthode appelée lors de la modification (update) d’un utilisateur existant
     * -> Empêche la suppression du rôle ADMIN si l’utilisateur le possédait déjà.
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof User) {
            return;
        }

        // Rôles en BDD avant la modification
        $originalData = $entityManager->getUnitOfWork()->getOriginalEntityData($entityInstance);
        $oldRoles = $originalData['roles'] ?? [];

        // Nouveaux rôles (après modification du formulaire)
        $newRoles = $entityInstance->getRoles();

        // Empêcher de retirer ROLE_ADMIN si l’utilisateur l’avait déjà
        if (\in_array('ROLE_ADMIN', $oldRoles, true) && !\in_array('ROLE_ADMIN', $newRoles, true)) {
            $newRoles[] = 'ROLE_ADMIN';
            $entityInstance->setRoles(array_unique($newRoles));
        }

        // Re-hash du mot de passe si modifié
        if ($entityInstance->getPassword()) {
            $hashedPassword = $this->passwordHasher->hashPassword(
                $entityInstance,
                $entityInstance->getPassword()
            );
            $entityInstance->setPassword($hashedPassword);
        }

        // Mise à jour de la relation avec Member, si besoin
        $member = $entityInstance->getPartisan();
        if ($member) {
            $member->setUser($entityInstance);
            $entityManager->persist($member);
        }

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }
}
