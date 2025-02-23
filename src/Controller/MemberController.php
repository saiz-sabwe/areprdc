<?php

namespace App\Controller;

use App\Entity\Member;
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
    private LoggerInterface $logger;

    public function __construct(MemberService $memberService, LoggerInterface $logger)
    {
        $this->memberService = $memberService;
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


        if ($member->getMemberCategory()->getLabel() === "effectif") {
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

        return $this->render('card/fiche.html.twig', [
            'member' => $member,
        ]);

    }
}
