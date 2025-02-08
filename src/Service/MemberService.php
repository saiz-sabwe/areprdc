<?php

namespace App\Service;

use App\Entity\Member;
use Exception;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\Palette\RGB;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Psr\Log\LoggerInterface;

class MemberService
{
    private LoggerInterface $logger;
    private ParameterBagInterface $parameterBag;

    public function __construct(LoggerInterface $logger, ParameterBagInterface $parameterBag)
    {
        $this->logger = $logger;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @throws Exception
     */
    public function generateMemberCard(Member $member): StreamedResponse
    {
        $projectDir = $this->parameterBag->get('kernel.project_dir');

        $status = $member->getStatus();
        $this->logger->info("status: ". $status);
        $category = $member->getMemberCategory()->getLabel();


        $imagePath = null;

        if ($status != 200) {
            $imagePath = $projectDir . '/public/assets/img/card/arriere.png';
        } else {
            if ($category == "honneur") {
                $imagePath = $projectDir . '/public/assets/img/card/honour_card_diamond.png';
            } else if ($category == "effectif") {
                $imagePath = $projectDir . '/public/assets/img/card/card_member.png';
            } else {
                $imagePath = $projectDir . '/public/assets/img/card/arriere.png';
            }
        }

        $fontPath = $projectDir . '/public/assets/font/Roboto-Bold.ttf';

        // Vérifier l'existence des fichiers requis
        if (!file_exists($imagePath) || !file_exists($fontPath)) {
            throw new NotFoundHttpException("Fichiers manquants.");
        }

        // Créer une instance de Imagine
        $imagine = new Imagine();
        $palette = new RGB();

        // Charger uniquement l'image d'honneur
        $image = $imagine->open($imagePath);

        // Définition des couleurs et police
        $black = $palette->color([0, 0, 0], 100);
        $red = $palette->color([255, 0, 0], 100);
        $fontSize = 40;

        $fontBlack = $imagine->font($fontPath, $fontSize, $black);
        $fontRed = $imagine->font($fontPath, $fontSize, $red);

        if ($status != 200) {

        } else {
            if ($category == "honneur") {
                // Ajouter le texte sur l'image
                $image->draw()
                    ->text('Prenom: '. $member->getFirstname(), $fontBlack, new Point(80, 1000))
                    ->text('Nom: '. $member->getName(), $fontBlack, new Point(80, 1100))
                    ->text('N° AREPRDC: '. $member->getReference(), $fontRed, new Point(80, 1200))
                    ->text('Adresse: '. $member->getEmail(), $fontBlack, new Point(80, 1300));
            } else if($category == "effectif") {
                // Ajouter le texte sur l'image
                $image->draw()
                    ->text('Prenom: '. $member->getFirstname(), $fontBlack, new Point(100, 500))
                    ->text('Nom: '. $member->getName(), $fontBlack, new Point(100, 600))
                    ->text('N° AREPRDC: '. $member->getReference(), $fontRed, new Point(100, 700))
                    ->text('Adresse: '. $member->getEmail(), $fontBlack, new Point(100, 800));
            } else{

            }
        }





        // Réponse directe avec l'image PNG
        return new StreamedResponse(function () use ($image) {
            $image->show('png');
        }, 200, ['Content-Type' => 'image/png']);
    }
}
