<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ManagerRegistry $doctrine, Request $request, PostRepository $repository): Response
    {
            //formulaire de recherche
            $search = $request->request->get("search"); // $_POST["search"]
            //on affiche tous les touitts
            $posts = $repository->findAll();// équivaut à un SELECT * FROM 'post'
            //si la variable est vide ou nulle on fait un findBy
            if ($search) {
                $posts = $repository->findBySearch($search); // équivaut à un SELECT * FROM 'post' WHERE title LIKE $search
            }
            
            return $this->render('post/index.html.twig', [
            //on envoie au template tous les posts
            "posts" => $posts 
            ]);
    }

    #[Route('/post/new')]
    // on injecte la requête HTTP
    public function create(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            // création du formulaire
            $post = new Post();
            $form = $this->createForm(PostType::class, $post);

            // mise à jour de l'objet $form avec les valeurs saisies
            $form->handleRequest($request);
            
            //on s'assure de la validité du formulaire et que les valeurs sont cohérentes
            if ($form->isSubmitted() && $form->isValid()) {

    //upload image dans Post
            // renomme l'image
            /** @var UploadedFile $image */
            $image = $form->get('image')->getData();

            $brochureFile = $form->get('image')->getData();
            // this condition is needed because the 'image' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

                // Déplace le fichier dans le répertoire /uploads, où sont stockés les images
                try {
                    $image->move(
                        $this->getParameter('uploads'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    dump($e);
                }
                // updates the 'imageFilename' property to store the PDF file name
                // instead of its contents
                $post->setImage($newFilename);
            }


        //on associe le post avec l'id de l'user qui l'a écrit
                $post->setUser($this->getUser());
                $post->setPublishedAt(new \DateTime());
                $em = $doctrine->getManager();
                $em->persist($post);
                $em->flush();
                // on redirige vers la page d'accueil
                return $this->redirectToRoute('home');
            }
            return $this->render('post/form.html.twig', [
                "form" => $form->createView()
            ]);
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


