<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Topic;
use App\Form\PostType;
use App\Entity\LikeStorage;
use App\Entity\ReportStorage;
use App\Repository\LikeStorageRepository;
use App\Repository\PostRepository;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReportStorageRepository;
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
     * @IsGranted("ROLE_ADMIN")
     */
    public function listPost(PostRepository $postRepository): Response
    {

        $posts = $postRepository->findAll();

        return $this->render('forum/post/list.html.twig', [
            'controller_name' => 'ForumController',
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/topic/{topic_id}/post/new", name="create_post")
     * @Route("/topic/{topic_id}/post/{post_id}/edit", name="edit_post")
     * 
     * @Paramconverter("topic", options={"mapping": {"topic_id" : "id"}})
     * @Paramconverter("post", options={"mapping": {"post_id" : "id"}})
     * 
     * @IsGranted("ROLE_USER")
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

            return $this->redirectToRoute('show_topic', ['id' => $post->getTopicId()->getId()]);
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
    public function showPost(Post $post): Response
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

        return $this->render('forum/topic/list.html.twig', [
            'controller_name' => 'ForumController',
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/post/{id}/like", name="like_post")
     * 
     * @Paramconverter("post", options={"mapping": {"id" : "id"}})
     * 
     *  @IsGranted("ROLE_USER")
     */
    public function like(Post $post, EntityManagerInterface $manager, LikeStorageRepository $likeStorageRepository)
    {
        $like = $likeStorageRepository->findOneBy(['userId' => $this->getUser(), 'postId' => $post->getId()]);

        if (!$like) {
            $like = New LikeStorage();
            $like->setUserId($this->getUser());
            $like->setPostId($post);
            $like->setValue('like');
            $post->addLike();
            $manager->persist($post);
            $manager->persist($like);
            $manager->flush();
        } elseif ($like->getValue() === 'dislike') {
            $manager->remove($like);
            $post->addLike();
            $manager->persist($post);
            $manager->flush();
            $this->like($post, $manager, $likeStorageRepository);
        } else {
            $post->removeLike();
            $manager->persist($post);
            $manager->remove($likeStorageRepository->findOneBy(['userId' => $this->getUser(), 'postId' => $post->getId()]));
            $manager->flush();
        }

        return $this->redirectToRoute('show_topic', ['id' => $post->getTopicId()->getId()]);
        

    }

    /**
     * @Route("/post/{id}/dislike", name="dislike_post")
     * 
     * @Paramconverter("post", options={"mapping": {"id" : "id"}})
     * 
     *  @IsGranted("ROLE_USER")
     */
    public function dislike(Post $post, EntityManagerInterface $manager, LikeStorageRepository $likeStorageRepository)
    {
        $like = $likeStorageRepository->findOneBy(['userId' => $this->getUser(), 'postId' => $post->getId()]);
        if (!$like) {
            $like = New LikeStorage();
            $like->setUserId($this->getUser());
            $like->setPostId($post);
            $like->setValue('dislike');
            $post->removeLike();
            $manager->persist($post);
            $manager->persist($like);
            $manager->flush();
        } elseif ($like->getValue() === 'like') {
            $manager->remove($like);
            $post->removeLike();
            $manager->persist($post);
            $manager->flush();
            $this->dislike($post, $manager, $likeStorageRepository);
        } else {
            $post->addLike();
            $manager->persist($post);
            $manager->remove($like);
            $manager->flush();
        }

        return $this->redirectToRoute('show_topic', ['id' => $post->getTopicId()->getId()]);

    }

    /**
     * @Route("/post/{id}/report", name="report_post")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function report(Post $post, EntityManagerInterface $manager)
    {
        if ($post->getReported() != TRUE) {
            $post->setReported(TRUE);
            $report = New ReportStorage;
            $report->setUserId($this->getUser());
            $report->setPostId($post);
            $report->setCreatedAt(New \DateTime());
            $manager->persist($report);
            $manager->flush();
        } else {
            throw new \Exception('This post was already reported');
        }
            return $this->redirectToRoute('show_topic', ['id' => $post->getTopicId()->getId()]);
    }

        /**
     * @Route("/post/{id}/unreport", name="unreport_post")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function unreport(Post $post, EntityManagerInterface $manager, ReportStorageRepository $reportStorageRepository)
    {
        if ($post->getReported() === TRUE) {
            $post->setReported(FALSE);
            $report = $reportStorageRepository->findOneBy(['postId' => $post]);
            $manager->remove($report);
            $manager->flush();
        } else {
            throw new \Exception('This post was already reported');
        }
        
            return $this->redirectToRoute('show_topic', ['id' => $post->getTopicId()->getId()]);
    }

}
