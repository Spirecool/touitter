<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException as ExceptionAccessDeniedException;

class PostController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ManagerRegistry $doctrine ): Response
    {
            //on récupère d'abord le Repository
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
        if ($this->getUser()) {
            // création du formulaire
            $post = new Post();
            $form = $this->createForm(PostType::class, $post);

            // mise à jour de l'objet $form avec les valeurs saisies
            $form->handleRequest($request);
            
            //on s'assure de la validité du formulaire et que les valeurs sont cohérentes
            if ($form->isSubmitted() && $form->isValid()) {
                //on associe le post avec l'id de l'user qui l'a écrit
                $post->setUser($this->getUser());
                $em = $doctrine->getManager();
                $em->persist($post);
                $em->flush();
                // on redirige vers la page d'accueil
                return $this->redirectToRoute('home');
            }
            return $this->render('user/form.html.twig', [
                "form" => $form->createView()
            ]);
        } else {
            return $this->redirectToRoute("login");
        }
    }

    #[Route('/post/delete/{id<\d+>}', name:'delete_post')]
    public function delete(Post $post, ManagerRegistry $doctrine): Response
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
         // si l'utilisateur connecté est différent de l'utilisateur du Post
         if ($this->getUser()!== $post->getUser()) {
            // throw new ExceptionAccessDeniedException("Vous n'avez pas l'autorisation d'accéder à cette fonctionnalité");
            // on redirige vers la page Home
            return $this->redirectToRoute('home');
        }
        $em = $doctrine->getManager();
        $em->remove($post);
        $em->flush();
        return $this->redirectToRoute('home');
    }

    #[Route('/post/edit/{id<\d+>}', name:'edit_post')]
    public function update(Post $post, Request $request, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->getUser()!== $post->getUser()) {
            $this->addFlash("error", "Vous ne pouvez pas modifier une publication qui n'est pas la vôtre");
            return $this->redirectToRoute("home");
        }
        
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('home');
        }
        return $this->render('post/form.html.twig', [
            "form" => $form->createView()
        ]);
    }

    #[Route('/post/copy/{id<\d+>}', name:'copy_post')]
    // on injecte la requête HTTP
    public function copy(Post $post,ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->getUser()!== $post->getUser()) {
            return $this->redirectToRoute('home');
        }
        $copyPost = clone $post;

        $em = $doctrine->getManager();
        $em->persist($copyPost);
        $em->flush();
        return $this->redirectToRoute('home');
    }
}



