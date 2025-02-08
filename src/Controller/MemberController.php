<?php

namespace App\Controller;

use App\Entity\Member;
use App\Service\MemberService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;

final class MemberController extends AbstractController
{

    private MemberService $memberService;

    public function __construct(MemberService $memberService)
    {
        $this->memberService = $memberService;
    }

    /**
     * @throws Exception
     */
    #[Route('/member/card/{id}', name: 'app_member_card')]
    public function generateMemberCard(Member $member):Response
    {

        return $this->memberService->generateMemberCard($member);

    }
}
