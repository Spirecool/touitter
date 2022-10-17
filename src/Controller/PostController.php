<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    #[Route('/')]
    public function index(ManagerRegistry $doctrine ): Response
    {
        //on récupère d'abord le Pository
        $repository = $doctrine->getRepository(Post::class);
        $posts = $repository->findAll();// SELECT * FROM 'post'
        return $this->render('post/index.html.twig', [
            //on envoie au template tous les posts
            "posts" => $posts 
        ]);
    }

    #[Route('/post/new')]
    // on injecte la requête HTTP
    public function create(Request $request, ManagerRegistry $doctrine): Response
    {
        // création du formulaire
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);

        // mise à jour de l'objet $form avec les valeurs saisies
        $form->handleRequest($request);
        
        //on s'assure de la validité du formulaire et que les valeurs sont cohérentes
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($post);
            $em->flush();
        }
        return $this->render('post/form.html.twig', [
            "post_form" => $form->createView()
        ]);
    }
}



