<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\Type\PostType;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Service\FileUploader;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    private $postRepository;

    private $commentRepository;

    public function __construct(PostRepository $postRepository, CommentRepository $commentRepository)
    {
       $this->postRepository = $postRepository;
       $this->commentRepository = $commentRepository;
    }

    /**
     * @Route("/create-post", name="post_create")
     */
    public function createPost(Request $request, FileUploader $fileUploader, ManagerRegistry $doctrine): Response 
    {
        $entityManager = $doctrine->getManager();
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $uploadFile = $form->get('upload')->getData();
            
            if ($uploadFile) {
                $newFilename = $fileUploader->upload($uploadFile);
                $post->setUploadFilename($newFilename);
            }
            $post = $form->getData();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('admin/createPost.html.twig', [
            'form' => $form->createView()
        ]);


    }


    /**
     * @Route("", name="admin_index")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/posts", name="admin_posts")
     */
    public function listPosts(): Response
    {
        $posts = $this->postRepository->findAll();
        
        return $this->render('admin/listPosts.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/comments", name="admin_comments")
     */
    public function listComments(): Response
    {
        $comments = $this->commentRepository->findBy([
            'reported' => true
        ]);

        return $this->render('admin/comments.html.twig', [
            'comments' => $comments
        ]);
    }

    /**
     * @Route("/comment-delete/{id}", name="comment_delete")
     */
    public function deleteComment(int $id, ManagerRegistry $doctrine): RedirectResponse
    {
        $entityManager = $doctrine->getManager();
        $comment = $this->commentRepository->find($id);
        if ($comment !== null) {

            $entityManager->remove($comment);
            $entityManager->flush();
        }

        $this->addFlash(
            'danger',
            'Commentaire supprimé'
        );
        

        return $this->redirectToRoute('admin_comments');
    }

    /**
     * @Route("/comment-valid/{id}", name="comment_valid")
     */
    public function validComment(int $id, ManagerRegistry $doctrine): RedirectResponse
    {
        $entityManager = $doctrine->getManager();
        $comment = $this->commentRepository->find($id);
        if ($comment !== null) {

            $comment->setReported(false);
            $entityManager->flush();
        }

        $this->addFlash(
            'success',
            'Commentaire validé'
        );

        return $this->redirectToRoute('admin_comments');
    }

    /**
     * @Route("/post-delete/{id}", name="post_delete")
     */
    public function deletePost(int $id, ManagerRegistry $doctrine): RedirectResponse
    {
        $entityManager = $doctrine->getManager();
        $post = $this->postRepository->find($id);   
        $entityManager->remove($post);
        $entityManager->flush();
        

        $this->addFlash(
            'danger',
            'Article supprimé'
        );
        

        return $this->redirectToRoute('admin_posts');
    }

    /**
     * @Route("/post-update/{id}", name="post_update")
     */
    public function updatePost(int $id, ManagerRegistry $doctrine, FileUploader $fileUploader, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $post = $this->postRepository->find($id);
        if (!$post) {
            throw $this->createNotFoundException(
                'Article inexistant '.$id
            );
        }
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $uploadFile = $form->get('upload')->getData();
            
            if ($uploadFile) {
                $newFilename = $fileUploader->upload($uploadFile);
                $post->setUploadFilename($newFilename);
            }
            
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Article modifié'
            );

            return $this->redirectToRoute('admin_posts');
        }

        return $this->render('admin/createPost.html.twig', [
            'form' => $form->createView()
        ]);





    }

}