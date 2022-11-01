<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user/new', name: 'user_new')]
    public function new(Request $request, UserPasswordHasherInterface $userPasswordHasher, ManagerRegistry $doctrine): Response
    {
        $user = new User($userPasswordHasher);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // //on récupère tous les utilisateurs
            // $repository = $doctrine->getRepository(User::class);
            // $users = $repository->findAll();
            // // on va les parcourir et si déjà existant on génère une erreur
            // foreach ($users as $u) {
            //     if ($u->getUsername() === $user->getUsername() ) {
            //         return new Exception("Ce nom d'utilisateur est déjà pris !");
            //     }
            // }
            $em = $doctrine->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('home');
        }
        return $this->render('user/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
