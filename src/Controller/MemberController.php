<?php

namespace App\Controller;

use App\Entity\Member;
use App\Service\InscriptionPaymentService;
use App\Service\MemberService;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;

final class MemberController extends AbstractController
{

    private MemberService $memberService;
    private InscriptionPaymentService $inscriptionPaymentService;
    private LoggerInterface $logger;

    public function __construct(MemberService $memberService, LoggerInterface $logger, InscriptionPaymentService $inscriptionPaymentService)
    {
        $this->memberService = $memberService;
        $this->inscriptionPaymentService = $inscriptionPaymentService;
        $this->logger = $logger;
    }

    /**
     * @throws Exception
     */
    #[Route('/member/card/{id}', name: 'app_member_card')]
    public function generateMemberCard(Member $member): Response
    {

//        return $this->render('card/fiche.html.twig', [
//            'member' => $member,
//        ]);


        if ($member->getMemberCategory()->getLabel() === "EFFECTIF") {
            return $this->render('card/effectif.html.twig', [
                'member' => $member,
            ]);
        } else {
            return $this->render('card/honneur.html.twig', [
                'member' => $member,
            ]);

        }

    }


    #[Route('/member/fiche/{id}', name: 'app_member_fiche')]
    public function generateMemberFiche(Member $member): Response
    {


        $inscriptionPayment = $this->inscriptionPaymentService->getByDateDelivered($member);

        $this->logger->info("<<< inscriptionPayment value :", ["inscriptionPayment"=>$inscriptionPayment->getEnrollee()]);


        return $this->render('card/fiche.html.twig', [
            'member' => $member,
            'inscriptionPayment' => $inscriptionPayment,
        ]);

    }
}
