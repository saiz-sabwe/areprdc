<?php

namespace App\Service;

use App\Entity\InscriptionPayment;
use App\Entity\Member;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class InscriptionPaymentService
{
    private LoggerInterface $logger;
    private ParameterBagInterface $parameterBag;
    private EntityManagerInterface $entityManager;

    public function __construct(LoggerInterface $logger, ParameterBagInterface $parameterBag, EntityManagerInterface $entityManager)
    {
        $this->logger = $logger;
        $this->parameterBag = $parameterBag;
        $this->entityManager = $entityManager;
    }

    public function getByDateDelivered(Member $member) : InscriptionPayment
    {

        return $this->entityManager->getRepository(InscriptionPayment::class)->findLatestByDateMatch($member);

    }


}