<?php

namespace App\Controller;

use App\Entity\Category;
use App\Services\HomeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    private HomeService $homeService;
    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->homeService=new HomeService($entityManagerInterface);
    }
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'categories' => $this->homeService->getCategories(),
        ]);
    }
}
