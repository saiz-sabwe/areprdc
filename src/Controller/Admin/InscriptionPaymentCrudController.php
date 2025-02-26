<?php

namespace App\Controller\Admin;

use App\Entity\InscriptionPayment;
use App\Repository\MemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\RequestStack;

class InscriptionPaymentCrudController extends AbstractCrudController
{
    private EntityManagerInterface $entityManager;
    private MemberRepository $memberRepository;
    private RequestStack $requestStack;

    public function __construct(
        EntityManagerInterface $entityManager,
        MemberRepository $memberRepository,
        RequestStack $requestStack
    ) {
        $this->entityManager = $entityManager;
        $this->memberRepository = $memberRepository;
        $this->requestStack = $requestStack;
    }

    public static function getEntityFqcn(): string
    {
        return InscriptionPayment::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        // Si l'utilisateur connecté n'est pas admin,
        // on désactive les actions NEW, EDIT et DELETE afin de n'autoriser
        // que la consultation (via DETAIL).
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $actions
                ->disable(Action::NEW, Action::EDIT, Action::DELETE)
                ->add(Crud::PAGE_INDEX, Action::DETAIL);
        }

        // Pour le compte Admin, toutes les actions sont autorisées.
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_NEW, Action::INDEX);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            DateTimeField::new('createdAt', 'Créé le')->onlyOnDetail(),
            IntegerField::new('amount'),
            // Si besoin, vous pouvez formater le MoneyField selon votre devise
            MoneyField::new('amount', 'Montant')->setCurrency('EUR'),
            AssociationField::new('currency', 'Devise')->setRequired(true),
            AssociationField::new('mode', 'Mode de Paiement'),
            TextField::new('reference'),
            AssociationField::new('enrollee', 'Membre')
                ->setFormTypeOptions([
                    'query_builder' => function (MemberRepository $er) {
                        return $er->findMemberExpired();
                    },
                ])
                ->setFormTypeOption('placeholder', 'Sélectionnez un membre'),
        ];
    }

    /**
     * Méthode appelée lors de la création d'un paiement d'inscription.
     */
    public function createEntity(string $entityFqcn)
    {
        $payment = new $entityFqcn();
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return $payment;
        }

        // Récupération des données envoyées dans le formulaire
        $formData = $request->request->all();

        // Vérifier si le champ enrollee est présent dans les données envoyées
        if (isset($formData['InscriptionPayment']['enrollee'])) {
            $memberId = $formData['InscriptionPayment']['enrollee'];

            // Récupérer le membre via le repository
            $member = $this->memberRepository->find($memberId);

            if ($member) {
                $member->setStatus(200); // Modifier son statut à 200
                $member->setUpdatedAt(new \DateTimeImmutable());
                $member->setDeliveredAt(new \DateTimeImmutable());
                $member->setExpiredAt((new \DateTimeImmutable())->modify('+1 year'));

                $this->entityManager->persist($member);
            }
        }

        $payment->setCreatedAt(new \DateTimeImmutable());

        return $payment;
    }

    /**
     * Méthode pour sauvegarder la modification du statut du membre.
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof InscriptionPayment) {
            $member = $entityInstance->getEnrollee();

            if ($member) {
                $member->setStatus(200);
                $entityManager->persist($member);
                $entityManager->flush();
            }
        }

        parent::persistEntity($entityManager, $entityInstance);
    }
}
