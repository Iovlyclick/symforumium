<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Topic;
use App\Form\PostType;
use App\Entity\LikeStorage;
use App\Repository\PostRepository;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


class PostController extends AbstractController
{
    /**
     * @Route("/post", name="all_post")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function index(PostRepository $postRepository): Response
    {

        $posts = $postRepository->findAll();

        return $this->render('forum/index.html.twig', [
            'controller_name' => 'ForumController',
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/topic/{topic_id}/post/new", name="post_create")
     * @Route("/topic/{topic_id}/post/{post_id}/edit", name="post_edit")
     * 
     * @Paramconverter("topic", options={"mapping": {"topic_id" : "id"}})
     * @Paramconverter("post", options={"mapping": {"post_id" : "id"}})
     */
    public function formPost(Topic $topic, Post $post = NULL, Request $request, EntityManagerInterface $manager)
    {
        
        if (!$post) {
            $post = new Post();
            $post->setTopicId($topic);
        }

        $form = $this->createForm(PostType::class, $post); 

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() ) {
            $post->setAuthor($this->getUser());
            if (!$post->getId()) {
                $current_date = new \DateTime();
                $post->setCreatedAt($current_date);
                $post->setLastEditedAt($current_date);
                $user = $this->getUser();
                $user->addPost($post);
                $manager->persist($user);
                $manager->flush();
            }

            $manager->persist($post);
            $manager->flush();

            return $this->redirectToRoute('show_post', ['id' => $post->getId()]);
        }
        return $this->render('forum/post/create.html.twig', [
            'formPost' => $form->createView(),
            'editMode' => $post->getId()!== NULL,
            'topic' => $topic->getId(),
        ]);
    }

    
        /**
     * @Route("/post/{id}", name="show_post")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function showPost(Post $post, TopicRepository $topicRepository): Response
    {


        return $this->render('forum/post/show.html.twig', [
            'controller_name' => 'ForumController',
            'post' => $post,
        ]);
    }


    /**
     * @Route("/post/{id}/delete", name="delete_post")
     * 
     *  @IsGranted("ROLE_USER")
     */
    public function deletePost(Post $post, EntityManagerInterface $manager, PostRepository $postRepository): Response
    {
        $manager->remove($post);
        $manager->flush();
        
        $posts = $postRepository->findAll();

        return $this->render('forum/index.html.twig', [
            'controller_name' => 'ForumController',
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/post/{id}/like", name="like_post")
     * 
     *  @IsGranted("ROLE_USER")
     */
    public function like(LikeStorage $like = NULL, Post $post, EntityManagerInterface $manager)
    {
        if (!$like) {
            $like = New LikeStorage();
            $like->setUserId($this->getUser());
            $like->setPostId($post);
            $post->addLike();
            $manager->persist($post);
            $manager->persist($like);
            $manager->flush();
        } else {
            $post->removeLike();
            $manager->persist($post);
            $manager->remove($like);
            $manager->flush();
        }

        return $this->redirectToRoute('show_post', ['id' => $post->getId()]);
    }

    /**
     * @Route("/post/{id}/dislike", name="dislike_post")
     * 
     *  @IsGranted("ROLE_USER")
     */
    public function dislike(LikeStorage $like = NULL, Post $post, EntityManagerInterface $manager)
    {
        if (!$like) {
            $like = New LikeStorage();
            $like->setUserId($this->getUser());
            $like->setPostId($post);
            $post->removeLike();
            $manager->persist($post);
            $manager->persist($like);
            $manager->flush();
        } else {
            $post->addLike();
            $manager->persist($post);
            $manager->remove($like);
            $manager->flush();
        }

        return $this->redirectToRoute('show_topic', ['id' => $post->getTopicId()->getId()]);
    }

}
