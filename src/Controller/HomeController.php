<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        
        $posts = $this->repository->findAll();
        $posts = $paginator->paginate($posts, $request->query->getInt('page', 1), 3);

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


