<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\PostRepository;
use App\Entity\Post;
use App\Form\CommentType;
use App\Form\Type\PostType;
use App\Repository\CommentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/post")
*/
class PostController extends AbstractController
{
    private $repository;

    private $commentRepository;

    public function __construct(PostRepository $postRepository, CommentRepository $commentRepository)
    {
       $this->repository = $postRepository;
       $this->commentRepository = $commentRepository;
    }

    /**
     * @Route("/", name="post_list")
     */
    public function getAll(): Response
    {
       
        $posts = $this->repository->findAll();
        
        return $this->render('blog/list.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/{id}", name="post_get")
     */
    public function show(int $id, Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $post = $this->repository->find($id);
        if ($post === null) {
            throw new NotFoundHttpException();
        }
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setPost($post);
            $comment->setUser($this->getUser());
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('post_get', ['id' => $id]);


            
        }


        
        return $this->render('blog/get.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/comment-report/{id}", name="comment_report")
     */
    public function reportComment(int $id, ManagerRegistry $doctrine): RedirectResponse
    {
        $entityManager = $doctrine->getManager();
        $comment = $this->commentRepository->find($id);
        if ($comment) {

            $comment->setReported(true);
            $entityManager->flush();
        }

        $this->addFlash(
            'danger',
            'Commentaire signalé'
        );

        return $this->redirectToRoute('post_get', ['id' => $comment->getPost()->getId()]);
    }
   

}


