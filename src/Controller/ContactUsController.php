<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ContactUsController extends AbstractController
{
    #[Route('/user/contact_us', name: 'contact_us.index')]
    public function index(): Response
    {
        return $this->render('contact_us/index.html.twig');
    }
}
