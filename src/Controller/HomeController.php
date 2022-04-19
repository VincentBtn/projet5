<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $repository;

    public function __construct(PostRepository $postRepository)
    {
       $this->repository = $postRepository;
    }

    /**
     * @Route("", name="home")
     */
    public function index(): Response
    {
        $posts = $this->repository->findAll();

        return $this->render('blog/index.html.twig', [
            'posts' => $posts
        ]);

        

    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(): Response
    {
        return $this->render('blog/contact.html.twig');
            
    }
   

}


