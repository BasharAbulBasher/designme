<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GastronomieController extends AbstractController
{
    #[Route('/user/gastronomie/digital', name: 'gastronomie.digital')]
    public function digital(): Response
    {
        return $this->render('gastronomie/digital.html.twig',);
    }
    #[Route('/user/gastronomie/design', name: 'gastronomie.design')]
    public function design(): Response
    {
        return $this->render('gastronomie/design.html.twig',);
    }
}
