<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comment;
use App\Entity\LikeStorage;
use App\Form\CommentType;
use App\Repository\PostRepository;
use App\Repository\TopicRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class CommentController extends AbstractController
{
    /**
     * @Route("/comment", name="all_comment")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function index(CommentRepository $commentRepository): Response
    {

        $comments = $commentRepository->findAll();

        return $this->render('forum/index.html.twig', [
            'controller_name' => 'ForumController',
            'comments' => $comments,
        ]);
    }

    /**
     * @Route("/post/{post_id}/comment/new", name="comment_create")
     * @Route("/post/{post_id}/comment/{comment_id}/edit", name="comment_edit")
     * 
     * @Paramconverter("post", options={"mapping": {"post_id" : "id"}})
     * @Paramconverter("comment", options={"mapping": {"comment_id" : "id"}})
     */
    public function formComment(Post $post, Comment $comment = NULL, Request $request, EntityManagerInterface $manager)
    {
        
        if (!$comment) {
            $comment = new Comment();
            $comment->setPostId($post);
        }

        $form = $this->createForm(CommentType::class, $comment); 

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() ) {
            $comment->setAuthor($this->getUser());
            if (!$comment->getId()) {
                $current_date = new \DateTime();
                $comment->setCreatedAt($current_date);
                $comment->setLastEditedAt($current_date);
                $user = $this->getUser();
                $user->addComment($comment);
                $manager->persist($user);
                $manager->flush();
            }

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('show_comment', ['id' => $comment->getId()]);
        }
        return $this->render('forum/comment/create.html.twig', [
            'formComment' => $form->createView(),
            'editMode' => $comment->getId()!== NULL,
            'post' => $post->getId(),
        ]);
    }

    
        /**
     * @Route("/comment/{id}", name="show_comment")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function showComment(Comment $comment, PostRepository $postRepository): Response
    {


        return $this->render('forum/comment/show.html.twig', [
            'controller_name' => 'ForumController',
            'comment' => $comment,
        ]);
    }


    /**
     * @Route("/comment/{id}/delete", name="delete_comment")
     * 
     *  @IsGranted("ROLE_USER")
     */
    public function deleteComment(Comment $comment, EntityManagerInterface $manager, TopicRepository $topicRepository): Response
    {
        $manager->remove($comment);
        $manager->flush();
        
        $topics = $topicRepository->findAll();

        return $this->render('forum/index.html.twig', [
            'controller_name' => 'ForumController',
            'topics' => $topics,
        ]);
    }

    /**
     * @Route("/comment/{id}/like", name="like_comment")
     * 
     *  @IsGranted("ROLE_USER")
     */
    public function like(LikeStorage $like = NULL, Comment $comment, EntityManagerInterface $manager)
    {
        if (!$like) {
            $like = New LikeStorage();
            $like->setUserId($this->getUser());
            $like->setCommentId($comment);
            $comment->addLike();
            $manager->persist($comment);
            $manager->persist($like);
            $manager->flush();
        } else {
            $comment->removeLike();
            $manager->persist($comment);
            $manager->remove($like);
            $manager->flush();
        }

        return $this->redirectToRoute('show_comment', ['id' => $comment->getId()]);
    }

        /**
     * @Route("/comment/{id}/dislike", name="dislike_comment")
     * 
     *  @IsGranted("ROLE_USER")
     */
    public function dislike(LikeStorage $like = NULL, Comment $comment, EntityManagerInterface $manager, PostRepository $postRepository)
    {
        if (!$like) {
            $like = New LikeStorage();
            $like->setUserId($this->getUser());
            $like->setCommentId($comment);
            $comment->removeLike();
            $manager->persist($comment);
            $manager->persist($like);
            $manager->flush();
        } else {
            $comment->addLike();
            $manager->persist($comment);
            $manager->remove($like);
            $manager->flush();
        }

        return $this->redirectToRoute('show_topic', ['id' => $postRepository->find($comment->getPostId())->getTopicId()->getId()]);
    }

}
