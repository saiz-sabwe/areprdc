<?php

namespace App\Controller\Admin;

use App\Entity\Address;
use App\Entity\City;
use App\Entity\Federation;
use App\Entity\Member;
use App\Controller\Admin\CityCrudController;
use App\Controller\Admin\FederationCrudController;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Vich\UploaderBundle\Form\Type\VichFileType;

class MemberCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Member::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        // Règles de permission :
        // - ROLE_ADMIN : accès complet
        // - ROLE_SERVICE_ADHESION : accès en création et modification (mais suppression interdite)
        // - Tous les autres rôles : accès en lecture seule (uniquement DETAIL)
        if ($this->isGranted('ROLE_ADMIN')) {
            return $actions
                ->add(Crud::PAGE_INDEX, Action::DETAIL)
                ->add(Crud::PAGE_NEW, Action::INDEX);
        } elseif ($this->isGranted('ROLE_SERVICE_ADHESION')) {
            return $actions
                ->disable(Action::DELETE)
                ->add(Crud::PAGE_INDEX, Action::DETAIL)
                ->add(Crud::PAGE_NEW, Action::INDEX);
        } else {
            return $actions
                ->disable(Action::NEW, Action::EDIT, Action::DELETE)
                ->add(Crud::PAGE_INDEX, Action::DETAIL);
        }
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // --- Informations sur le membre ---
            FormField::addPanel('Identité du membre')->collapsible(),
            IdField::new('id')->hideOnForm()->hideOnIndex(),
            DateTimeField::new('createdAt', 'Créé le')->onlyOnDetail(),

            TextField::new('reference', 'Numéro')
                ->setFormTypeOption('disabled', true)
                ->onlyOnIndex(),

            Field::new('imageFile', 'Photo')
                ->setFormType(VichFileType::class)
                ->setFormTypeOptions(['required' => false])
                ->onlyOnForms(),

            TextField::new('firstname', 'Prénom')->setRequired(true),
            TextField::new('name', 'Nom')->setRequired(true),
            TextField::new('lastname', 'Postnom')
                ->setRequired(true)
                ->hideOnIndex(),
            DateTimeField::new('deliveredAt', 'Date livraison')
                ->setRequired(true)
                ->onlyOnDetail(),
            DateTimeField::new('expiredAt', 'Date d\'expiration')
                ->setRequired(true)
                ->onlyOnDetail(),
            ChoiceField::new('gender', 'Sexe')
                ->setChoices(['Homme' => 'M', 'Femme' => 'F'])
                ->setRequired(true),
            EmailField::new('email')->setRequired(false)->hideOnIndex(),

            TextField::new('placeOfBirth', 'Lieu de naissance')->hideOnIndex(),
            TextField::new('country', 'Pays')->hideOnIndex(),
            TextField::new('phone', 'Téléphone')->hideOnIndex(),
            TextField::new('education', 'Niveau d\'études')->hideOnIndex(),
            AssociationField::new('position', 'Fonction'),
            TextField::new('affiliation', 'Affiliation politique')->hideOnIndex(),

            DateTimeField::new('dateOfBirth', 'Date de naissance')
                ->setRequired(false)
                ->hideOnIndex(),

            DateTimeField::new('updatedAt', 'Dernière mise à jour')->onlyOnDetail(),

            FormField::addPanel('Origine du membre')->collapsible(),
            AssociationField::new('memberCategory', 'Catégorie')
                ->setRequired(true),
            AssociationField::new('province', 'Province')
                ->setRequired(true)
                ->hideOnIndex(),
            AssociationField::new('territory', 'Territoire')
                ->setRequired(true)
                ->hideOnIndex(),
            AssociationField::new('sector', 'Secteur')
                ->setRequired(false)
                ->hideOnIndex(),
            AssociationField::new('community', 'Groupement')
                ->setRequired(false)
                ->hideOnIndex(),

            // --- Informations sur l'adresse ---
            FormField::addPanel('Adresse du membre')->collapsible(),

            TextField::new('address.municipality', 'Commune')
                ->setRequired(false)
                ->hideOnIndex(),
            TextField::new('address.neighborhood', 'Quartier')
                ->setRequired(false)
                ->hideOnIndex(),
            TextField::new('address.avenue', 'Avenue')
                ->setRequired(true),
            TextField::new('address.number', 'Numéro')
                ->setRequired(true),

            AssociationField::new('address.city', 'Ville')
                ->setRequired(true)
                ->setCrudController(CityCrudController::class)
                ->hideOnIndex(),

            AssociationField::new('address.federation', 'Fédération')
                ->setRequired(true)
                ->setCrudController(FederationCrudController::class)
                ->hideOnIndex(),

            UrlField::new('email', 'Carte')
                ->setVirtual(true)
                ->formatValue(function ($value, $entity) {
                    return sprintf(
                        '<a href="/member/card/%s" target="_blank">Voir Carte</a>',
                        $entity->getId()
                    );
                })
                ->onlyOnIndex(),

            UrlField::new('email', 'Fiche')
                ->setVirtual(true)
                ->formatValue(function ($value, $entity) {
                    return sprintf(
                        '<a href="/member/fiche/%s" target="_blank">Voir Fiche</a>',
                        $entity->getId()
                    );
                })
                ->onlyOnIndex(),
        ];
    }

    /**
     * Lors de la création d'un Member, on s'assure que l'instance d'Address est créée.
     */
    public function createEntity(string $entityFqcn)
    {
        $member = new Member();
        $address = new Address();
        $member->setAddress($address);
        return $member;
    }

    /**
     * Lors de l'édition, si l'Address n'est pas initialisée, on lui affecte une nouvelle instance.
     */
    public function edit(AdminContext $context)
    {
        $member = $context->getEntity()->getInstance();
        if (null === $member->getAddress()) {
            $member->setAddress(new Address());
        }
        return parent::edit($context);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Member) {
            return;
        }

        // Mise à jour de la date de modification
        $entityInstance->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Member) {
            return;
        }

        // Persistance de l'Address et de ses associations
        if ($entityInstance->getAddress()) {
            $address = $entityInstance->getAddress();
            if ($address->getCity()) {
                $entityManager->persist($address->getCity());
            }
            if ($address->getFederation()) {
                $entityManager->persist($address->getFederation());
            }
            $entityManager->persist($address);
        }

        // Génération du numéro AREP si la province et la catégorie sont renseignées
        if ($entityInstance->getProvince() && $entityInstance->getMemberCategory()) {
            $lastMember = $entityManager->getRepository(Member::class)->findOneBy([], ['id' => 'DESC']);
            $lastNumber = $lastMember ? intval(substr($lastMember->getReference(), 5, 4)) + 1 : 1;

            $categoryIndex = intdiv($lastNumber - 1, 1000);
            $categoryCode = chr(65 + intdiv($categoryIndex, 26)) . chr(65 + ($categoryIndex % 26));

            $provinceCode = strtoupper(substr($entityInstance->getProvince()->getLabel(), 0, 3));
            $newReference = sprintf("AREP %04d-%s/%s", $lastNumber, $provinceCode, $categoryCode);

            $entityInstance->setReference($newReference);
        }

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }
}
