<?php 
namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthService
{
    /**
     * Register
     */
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        //Chick if Email exists
        if(!empty($entityManager->getRepository(User::class)->findByEmail($request->get('email'))))
        {
            return [
                'success'=>false,
                'message'=>'This Email exists..'
            ];
        }
        //Register new Admin
        $user=new User();
        //Get the PlaintextPassword
        $plaintextPassword=$request->get('password');
        //Hash the PlaintextPassword
        $hashedPassword=$passwordHasher->hashPassword($user, $plaintextPassword);
        //Set the HashedPassword
        $user->setPassword($hashedPassword);
        //Set the Email
        $user->setEmail($request->get('email'));
        //Set the ROLLE
        $user->setRoles(['ROLE_ADMIN']);
        $entityManager->persist($user);
        $entityManager->flush();
        return[
            'success'=>true,
            'message'=>'New Admin created Successfuly'
        ];
    }
    /**
     * Login
     */
    public function login(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, Security $security)
    {
        //Get User
        $user=$entityManager->getRepository(User::class)->findByEmail($request->get('email'));
        //Check if User Exists
        if(empty($user))
        {
            return [
                'success'=>false,
                'message'=>'This Email dose NOT exist!!'
            ];
        }
        //Chick if the Password is Valid
        if(!$passwordHasher->isPasswordValid($user,$request->get('password')))
        {
            return [
                'success'=>false,
                'message'=>'The Password is Wrong!!'
            ];
        }
        $security->login($user);
        return[
            'success'=>true,
            'message'=>'Successfully logedin'
        ];
    }
}