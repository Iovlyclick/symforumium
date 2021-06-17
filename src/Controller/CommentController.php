<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Entity\LikeStorage;
use App\Entity\ReportStorage;
use App\Repository\PostRepository;
use App\Repository\TopicRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\LikeStorageRepository;
use App\Repository\ReportStorageRepository;
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
     * @IsGranted("ROLE_ADMIN")
     */
    public function listComment(CommentRepository $commentRepository): Response
    {

        $comments = $commentRepository->findAll();

        return $this->render('forum/comment/index.html.twig', [
            'controller_name' => 'ForumController',
            'comments' => $comments,
        ]);
    }

    /**
     * @Route("/post/{post_id}/comment/new", name="create_comment")
     * @Route("/post/{post_id}/comment/{comment_id}/edit", name="edit_comment")
     * 
     * @Paramconverter("post", options={"mapping": {"post_id" : "id"}})
     * @Paramconverter("comment", options={"mapping": {"comment_id" : "id"}})
     */
    public function formComment(Post $post, Comment $comment = NULL, Request $request, EntityManagerInterface $manager, PostRepository $postRepository)
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

            $topicId = $postRepository->find($comment->getPostId())->getTopicId()->getId();

            return $this->redirectToRoute('show_topic', ['id' => $comment->getPostId()->getTopicId()->getId()]);
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

        return $this->render('forum/topic/list.html.twig', [
            'controller_name' => 'ForumController',
            'topics' => $topics,
        ]);
    }

    /**
     * @Route("/comment/{id}/like", name="like_comment")
     * 
     * @Paramconverter("comment", options={"mapping": {"id" : "id"}})
     * 
     *  @IsGranted("ROLE_USER")
     */
    public function like(Comment $comment, EntityManagerInterface $manager, LikeStorageRepository $likeStorageRepository)
    {
        $like = $likeStorageRepository->findOneBy(['userId' => $this->getUser(), 'commentId' => $comment->getId()]);

        if (!$like) {
            $like = New LikeStorage();
            $like->setUserId($this->getUser());
            $like->setCommentId($comment);
            $like->setValue('like');
            $comment->addLike();
            $manager->persist($comment);
            $manager->persist($like);
            $manager->flush();
        } elseif ($like->getValue() === 'dislike') {
            $manager->remove($like);
            $comment->addLike();
            $manager->persist($comment);
            $manager->flush();
            $this->like($comment, $manager, $likeStorageRepository);
        } else {
            $comment->removeLike();
            $manager->persist($comment);
            $manager->remove($likeStorageRepository->findOneBy(['userId' => $this->getUser(), 'commentId' => $comment->getId()]));
            $manager->flush();
        }

        // return $this->redirectToRoute('show_topic', ['id' => $comment->getTopicId()->getId()]);
        return $this->redirectToRoute('show_comment', ['id' => $comment->getId()]);

    }

    /**
     * @Route("/comment/{id}/dislike", name="dislike_comment")
     * 
     * @Paramconverter("comment", options={"mapping": {"id" : "id"}})
     * 
     *  @IsGranted("ROLE_USER")
     */
    public function dislike(Comment $comment, EntityManagerInterface $manager, LikeStorageRepository $likeStorageRepository)
    {
        $like = $likeStorageRepository->findOneBy(['userId' => $this->getUser(), 'commentId' => $comment->getId()]);
        if (!$like) {
            $like = New LikeStorage();
            $like->setUserId($this->getUser());
            $like->setCommentId($comment);
            $like->setValue('dislike');
            $comment->removeLike();
            $manager->persist($comment);
            $manager->persist($like);
            $manager->flush();
        } elseif ($like->getValue() === 'like') {
            $manager->remove($like);
            $comment->removeLike();
            $manager->persist($comment);
            $manager->flush();
            $this->dislike($comment, $manager, $likeStorageRepository);
        } else {
            $comment->addLike();
            $manager->persist($comment);
            $manager->remove($like);
            $manager->flush();
        }

        // return $this->redirectToRoute('show_topic', ['id' => $comment->getTopicId()->getId()]);
        return $this->redirectToRoute('show_comment', ['id' => $comment->getId()]);

    }

    /**
     * @Route("/comment/{id}/report", name="report_comment")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function report(Comment $comment, EntityManagerInterface $manager)
    {
        if ($comment->getReported() != TRUE) {
            $comment->setReported(TRUE);
            $report = New ReportStorage;
            $report->setUserId($this->getUser());
            $report->setCommentId($comment);
            $report->setCreatedAt(New \DateTime());
            $manager->persist($report);
            $manager->flush();
        } else {
            throw new \Exception('This comment was already reported');
        }
            return $this->redirectToRoute('show_topic', ['id' => $comment->getPostId()->getTopicId()->getId()]);
    }

        /**
     * @Route("/comment/{id}/unreport", name="unreport_comment")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function unreport(Comment $comment, EntityManagerInterface $manager, ReportStorageRepository $reportStorageRepository)
    {
        if ($comment->getReported() === TRUE) {
            $comment->setReported(FALSE);
            $report = $reportStorageRepository->findOneBy(['commentId' => $comment]);
            $manager->remove($report);
            $manager->flush();
        } else {
            throw new \Exception('This comment was already reported');
        }
        
            return $this->redirectToRoute('show_topic', ['id' => $comment->getPostId()->getTopicId()->getId()]);
    }

}
