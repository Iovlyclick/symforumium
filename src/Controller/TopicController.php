<?php

namespace App\Controller;

use App\Entity\Topic;
use App\Form\TopicType;
use App\Entity\LikeStorage;
use App\Entity\ReportStorage;
use App\Repository\CommentRepository;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\LikeStorageRepository;
use App\Repository\PostRepository;
use App\Repository\ReportStorageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class TopicController extends AbstractController
{
    /**
     * @Route("/topic", name="all_topic")
     * 
     *  @IsGranted("ROLE_USER")
     */
    public function listTopic(TopicRepository $topicRepository): Response
    {

        $topics = $topicRepository->findBy(['reported' => NULL, 'archived' => NULL], ['likeAmount' => 'DESC']);

        return $this->render('forum/topic/list.html.twig', [
            'controller_name' => 'TopicController',
            'topics' => $topics,
        ]);
    }

    /**
     * @Route("/topic/new", name="create_topic")
     * @Route("/topic/{id}/edit", name="edit_topic")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function formTopic(Topic $topic = NULL, Request $request, EntityManagerInterface $manager)
    {
        if (!$topic) {
            $topic = new Topic();
        } elseif (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {

            if ($topic->getLikeAmount() !== 0 || $topic->getReported() === TRUE || strtotime($topic->getCreatedAt()->format('Y-m-d H:i:s')) < strtotime('-30 minutes')) {
                return $this->redirectToRoute('show_topic', ['id' => $topic->getId()]);
            }
        }

        $form = $this->createForm(TopicType::class, $topic);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$topic->getId()) {
                $topic->setAuthor($this->getUser());
                $current_date = new \DateTime();
                $topic->setCreatedAt($current_date);
                $topic->setLastEditedAt($current_date);
            }

            $manager->persist($topic);
            $manager->flush();

            return $this->redirectToRoute('show_topic', ['id' => $topic->getId()]);
        }
        return $this->render('/forum/topic/create.html.twig', [
            'formTopic' => $form->createView(),
            'editMode' => $topic->getId() !== NULL,
        ]);
    }


    /**
     * @Route("/topic/{id}", name="show_topic")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function showTopic(Topic $topic, PostRepository $postRepository, CommentRepository $commentRepository): Response
    {
        $posts = $postRepository->findBy(['topicId' => $topic->getId(), 'reported' => NULL, 'archived' => NULL], ['likeAmount' => 'DESC']);

        $comments = [];
        foreach ($posts as $post) {
            $comments[$post->getId()] = $commentRepository->findBy(['postId' => $post->getId(), 'reported' => NULL, 'archived' => NULL], ['likeAmount' => 'DESC']);
        }

        return $this->render('forum/topic/show.html.twig', [
            'controller_name' => 'TopicController',
            'topic' => $topic,
            'posts' => $posts,
            'comments' => $comments,
        ]);
    }


    /**
     * @Route("/topic/{id}/delete", name="delete_topic")
     * 
     *  @IsGranted("ROLE_USER")
     */
    public function deleteTopic(Topic $topic, EntityManagerInterface $manager, TopicRepository $topicRepository): Response
    {
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            if ($topic->getLikeAmount() !== 0 || $topic->getReported() === TRUE || strtotime($topic->getCreatedAt()->format('Y-m-d H:i:s')) < strtotime('-30 minutes')) {

                return $this->redirectToRoute('show_topic', ['id' => $topic->getId()]);
            }
        }
        $topic->setArchived(TRUE);
        $manager->persist($topic);
        $manager->flush();

        return $this->redirectToRoute('show_topic', ['id' => $topic->getId()]);
    }

    /**
     * @Route("/topic/{id}/like", name="like_topic")
     * 
     * @Paramconverter("topic", options={"mapping": {"id" : "id"}})
     * 
     *  @IsGranted("ROLE_USER")
     */
    public function like(Topic $topic, EntityManagerInterface $manager, LikeStorageRepository $likeStorageRepository)
    {
        $like = $likeStorageRepository->findOneBy(['userId' => $this->getUser(), 'topicId' => $topic->getId()]);

        if (!$like) {
            $like = new LikeStorage();
            $like->setUserId($this->getUser());
            $like->setTopicId($topic);
            $like->setValue('like');
            $topic->addLike();
            $manager->persist($topic);
            $manager->persist($like);
            $manager->flush();
        } elseif ($like->getValue() === 'dislike') {
            $manager->remove($like);
            $topic->addLike();
            $manager->persist($topic);
            $manager->flush();
            $this->like($topic, $manager, $likeStorageRepository);
        } else {
            $topic->removeLike();
            $manager->persist($topic);
            $manager->remove($likeStorageRepository->findOneBy(['userId' => $this->getUser(), 'topicId' => $topic->getId()]));
            $manager->flush();
        }

        return $this->redirectToRoute('show_topic', ['id' => $topic->getId()]);
    }

    /**
     * @Route("/topic/{id}/dislike", name="dislike_topic")
     * 
     * @Paramconverter("topic", options={"mapping": {"id" : "id"}})
     * 
     *  @IsGranted("ROLE_USER")
     */
    public function dislike(Topic $topic, EntityManagerInterface $manager, LikeStorageRepository $likeStorageRepository)
    {
        $like = $likeStorageRepository->findOneBy(['userId' => $this->getUser(), 'topicId' => $topic->getId()]);
        if (!$like) {
            $like = new LikeStorage();
            $like->setUserId($this->getUser());
            $like->setTopicId($topic);
            $like->setValue('dislike');
            $topic->removeLike();
            $manager->persist($topic);
            $manager->persist($like);
            $manager->flush();
        } elseif ($like->getValue() === 'like') {
            $manager->remove($like);
            $topic->removeLike();
            $manager->persist($topic);
            $manager->flush();
            $this->dislike($topic, $manager, $likeStorageRepository);
        } else {
            $topic->addLike();
            $manager->persist($topic);
            $manager->remove($like);
            $manager->flush();
        }

        return $this->redirectToRoute('show_topic', ['id' => $topic->getId()]);
    }

    /**
     * @Route("/topic/{id}/report", name="report_topic")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function report(Topic $topic, EntityManagerInterface $manager)
    {
        if ($topic->getReported() != TRUE) {
            $topic->setReported(TRUE);
            $report = new ReportStorage;
            $report->setUserId($this->getUser());
            $report->setTopicId($topic);
            $report->setCreatedAt(new \DateTime());
            $manager->persist($report);
            $manager->flush();
        } else {
            throw new \Exception('This topic was already reported');
        }

        return $this->redirectToRoute('all_topic');
    }

    /**
     * @Route("/topic/{id}/unreport", name="unreport_topic")
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function unreport(Topic $topic, EntityManagerInterface $manager, ReportStorageRepository $reportStorageRepository)
    {
        if ($topic->getReported() === TRUE) {
            $topic->setReported(NULL);
            $report = $reportStorageRepository->findOneBy(['topicId' => $topic]);
            $manager->remove($report);
            $manager->flush();
        } else {
            throw new \Exception('This topic was already reported');
        }

        return $this->redirectToRoute('show_topic', ['id' => $topic->getId()]);
    }

    /**
     * @Route("/admin", name="admin")
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function adminPanel(TopicRepository $topicRepository, PostRepository $postRepository, CommentRepository $commentRepository)
    {

        $topics = $topicRepository->findBy(['reported' => 1]);
        $posts = $postRepository->findBy(['reported' => 1]);
        $comments = $commentRepository->findBy(['reported' => 1]);

        return $this->render('forum/admin/list.html.twig', [
            'controller_name' => 'TopicController',
            'topics' => $topics,
            'posts' => $posts,
            'comments' => $comments,
        ]);
    }
}
