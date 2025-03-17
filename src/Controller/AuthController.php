<?php

namespace App\Controller;

use App\Services\AuthService;
use Doctrine\DBAL\Driver\Mysqli\Initializer\Secure;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class AuthController extends AbstractController
{
    private AuthService $authService;
    public function __construct()
    {
        $this->authService=new AuthService;
    }
    #[Route('/login', name: 'auth.login')]
    public function login(Request $request): Response
    {
        return $this->render('auth/login.html.twig',[
            'data'=>$request->get('data')
        ]);
    }
    #[Route('/loginTest', name: 'auth.loginTest')]
    public function loginTest(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface  $passwordHasher, Security $security): Response
    {
        $data=$this->authService->login($request, $entityManager,$passwordHasher,$security);
        if($data['success'])
        {
            return $this->redirectToRoute('admin.index');
        }
        return $this->redirectToRoute('auth.login',[
            'data'=>$this->authService->login($request, $entityManager,$passwordHasher,$security)
        ]);
    }
    #[Route('/register', name: 'auth.register')]
    public function register(Request $request): Response
    {
        return $this->render('auth/register.html.twig',[
            'data'=>$request->get('data')
        ]);
    }
    #[Route('/registerTest', name: 'auth.registerTest', methods:['post'])]
    public function registerTest(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        return $this->redirectToRoute('auth.register',[
         'data'=>$this->authService->register($request, $entityManager, $passwordHasher),
        ]);
    }
    #[Route('/logout', name: 'auth.logout')]
    public function logout()
    {
       return $this->render('base.html.twig');
    }
}
