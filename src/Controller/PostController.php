<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/')]
    public function index(): Response
    {
        return $this->render('post/index.html.twig');
    }

    #[Route('/post/new')]
    // on injecte la requête HTTP
    public function create(Request $request): Response
    {
        // création du formulaire
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);

        // mise à jour de l'objet $form avec les valeurs saisies
        $form->handleRequest($request);
        
        //on s'assure de la validité du formulaire et que les valeurs sont cohérentes
        if ($form->isSubmitted() && $form->isValid()) {
            // dump($post);
        }
        return $this->render('post/form.html.twig', [
            "post_form" => $form->createView()
        ]);
    }
}



