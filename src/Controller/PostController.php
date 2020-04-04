<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


class PostController extends AbstractController
{
    /** @var PostRepository $postRepository */
    private $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @Route("/posts", name="homepage")
     */
    public function posts()
    {
        $posts = $this->postRepository->findAll();

        return $this->render('posts/index.html.twig', [
            'posts' => $posts
        ]);
    }
    
    /**
     * @Route("/posts/new", name="new_blog_post")
     */
    public function addPost(Request $request, SluggerInterface $slugger)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setSlug($slugger->slug($post->getTitle())->lower());
            $post->setCreatedAt(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }
        return $this->render('posts/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
    /**
     * @Route("/posts/search", name="blog_search")
     */
    public function search(Request $request)
    {
        $query = $request->query->get('search');
        $posts = $this->postRepository->searchByQuery($query);

        return $this->render('blog/query_post.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/posts/{slug}", name="blog_show")
     */
    public function post(Post $post)
    {
        return $this->render('posts/show.html.twig', [
            'post' => $post
        ]);
    }

    /**
     * @Route("/posts/{slug}/edit", name="blog_post_edit")
     */
    public function edit(Post $post, Request $request, SluggerInterface $slugger)
    {
         $form = $this->createForm(PostType::class, $post);
         $form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid()) {
            $post->setSlug($slugger->slug($post->getTitle())->lower());
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('blog_show', [
                'slug' => $post->getSlug()
            ]);
         }

         return $this->render('posts/new.html.twig', [
             'form' => $form->createView()
         ]);
    }

     /**
     * @Route("/posts/{slug}/delete", name="blog_post_delete")
     */
    public function delete(Post $post)
    {     
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();

        return $this->redirectToRoute('homepage');
    }

}
