<?php

namespace App\Controller\Admin;

use App\Entity\Address;
use App\Entity\City;
use App\Entity\Federation;
use App\Entity\Member;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Doctrine\ORM\EntityManagerInterface;

class MemberCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Member::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            // ID caché dans le formulaire car généré automatiquement
            IdField::new('id')->hideOnForm(),

            // Numéro AREP (Référence), affiché seulement dans l'index, en lecture seule
            TextField::new('reference', 'Numéro AREP')
                ->setFormTypeOption('disabled', true)
                ->onlyOnIndex(),

            // Informations personnelles
            TextField::new('firstname', 'Prénom')->setRequired(true),
            TextField::new('name', 'Nom')->setRequired(true),
            TextField::new('lastname', 'Postnom')->setRequired(true),
            ChoiceField::new('gender', 'Sexe')->setChoices(['Homme' => 'H', 'Femme' => 'F'])->setRequired(true),
            EmailField::new('email')->setRequired(false),

            TextField::new('placeOfBirth', 'Lieu de naissance')->hideOnIndex(),
            TextField::new('country', 'Pays')->hideOnIndex(),
            TextField::new('phone', 'Téléphone')->hideOnIndex(),
            TextField::new('education', 'Niveau d\'études')->hideOnIndex(),
            TextField::new('affiliation', 'Affiliation politique')->hideOnIndex(),



            // Date de naissance (facultatif)
            DateTimeField::new('dateOfBirth', 'Date de naissance')->setRequired(false)->hideOnIndex(),

            // Relations avec d'autres entités
            FormField::addPanel('Origine du membre')->collapsible(),
            AssociationField::new('memberCategory', 'Catégorie Membre')->setRequired(true),
            AssociationField::new('province', 'Province')->setRequired(true)->hideOnIndex(),
            AssociationField::new('territory', 'Territoire')->setRequired(true)->hideOnIndex(),
            AssociationField::new('sector', 'Secteur')->setRequired(false)->hideOnIndex(),
            AssociationField::new('community', 'Groupement')->setRequired(false)->hideOnIndex(),

//            FormField::addPanel('Adresse du membre')->collapsible(),
//
//           // AssociationField::new('address', 'Adresse')->setRequired(false)->hideOnIndex(),
//            TextField::new('address.municipality', 'Municipalité')->setRequired(false),
//            TextField::new('address.neighborhood', 'Quartier')->setRequired(false),
//            TextField::new('address.avenue', 'Avenue')->setRequired(false),
//            TextField::new('address.number', 'Numéro')->setRequired(false),
//            AssociationField::new('address.city', 'Ville')->setRequired(true),
//            AssociationField::new('address.federation', 'Fédération')->setRequired(true),

            // Métadonnées (Dates de création et mise à jour, affichées uniquement en détail)
            DateTimeField::new('createdAt', 'Créé le')->onlyOnDetail(),
            DateTimeField::new('updatedAt', 'Dernière mise à jour')->onlyOnDetail(),

            // Carte de Membre - Champ Virtuel, lien vers la carte (affiché uniquement dans l'index)
            UrlField::new('email', 'Carte de Membre')
                ->setVirtual(true)
                ->formatValue(function ($value, $entity) {
                    return sprintf('<a href="/member/card/%s" target="_blank">Voir Carte</a>', $entity->getId());
                })
                ->onlyOnIndex(),
        ];
    }
//
//    public function createEntity(string $entityFqcn)
//    {
//        $member = new Member();
//
//        // Vérifier et initialiser Address si elle est null
//        if ($member->getAddress() === null) {
//            $address = new Address();
//
//            // Initialiser la ville et la fédération avec des objets vides pour éviter l'erreur NULL
//            $address->setCity(new City());
//            $address->setFederation(new Federation());
//
//            $member->setAddress($address);
//        }
//
//        return $member;
//    }



    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Member) {
            return;
        }

        // Vérifier que la province et la catégorie sont bien définies avant de générer le numéro AREP
        if ($entityInstance->getProvince() && $entityInstance->getMemberCategory()) {
            // Récupérer le dernier membre enregistré
            $lastMember = $entityManager->getRepository(Member::class)->findOneBy([], ['id' => 'DESC']);
            $lastNumber = $lastMember ? intval(substr($lastMember->getReference(), 5, 4)) + 1 : 1;

            // Déterminer le code de la catégorie en fonction du nombre de membres
            $categoryIndex = intdiv($lastNumber - 1, 1000); // Change tous les 1000 membres
            $categoryCode = chr(65 + intdiv($categoryIndex, 26)) . chr(65 + ($categoryIndex % 26)); // AA, AB, AC...

            // Générer le numéro de membre
            $provinceCode = strtoupper(substr($entityInstance->getProvince()->getLabel(), 0, 3)); // KIN par exemple
            $newReference = sprintf("AREP %04d-%s/%s", $lastNumber, $provinceCode, $categoryCode);

            // Appliquer le numéro de membre
            $entityInstance->setReference($newReference);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }


}
