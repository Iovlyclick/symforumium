<?php

namespace App\Controller;

use App\Entity\Topic;
use App\Form\TopicType;
use App\Entity\LikeStorage;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function index(TopicRepository $topicRepository): Response
    {

        $topics = $topicRepository->findAll();

        return $this->render('forum/index.html.twig', [
            'controller_name' => 'TopicController',
            'topics' => $topics,
        ]);
    }

    /**
     * @Route("/topic/new", name="create_topic")
     * @Route("/topic/{id}/edit", name="edit_topic")
     * 
     * @IsGranted("ROLE_USER")
     * 
     */
    public function formTopic(Topic $topic = NULL, Request $request, EntityManagerInterface $manager)
    {
        
        if (!$topic) {
            $topic = new Topic();
        }

        $form = $this->createForm(TopicType::class, $topic); 

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() ) {
            $topic->setAuthor($this->getUser());
            if (!$topic->getId()) {
                $current_date = new \DateTime();
                $topic->setCreatedAt($current_date);
                $topic->setLastEditedAt($current_date);
            }

            $manager->persist($topic);
            $manager->flush();

            return $this->redirectToRoute('show_topic', ['id' => $topic->getId()]);
        }
        return $this->render('topic/create.html.twig', [
            'formTopic' => $form->createView(),
            'editMode' => $topic->getId()!== NULL,
        ]);
    }

    
        /**
     * @Route("/topic/{id}", name="show_topic")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function showTopic(Topic $topic): Response
    {


        return $this->render('forum/show.html.twig', [
            'controller_name' => 'TopicController',
            'topic' => $topic,
        ]);
    }


    /**
     * @Route("/topic/{id}/delete", name="delete_topic")
     * 
     *  @IsGranted("ROLE_USER")
     */
    public function deleteTopic(Topic $topic, EntityManagerInterface $manager, TopicRepository $topicRepository): Response
    {
        $manager->remove($topic);
        $manager->flush();
        
        $topics = $topicRepository->findAll();

        return $this->render('forum/index.html.twig', [
            'controller_name' => 'TopicController',
            'topics' => $topics,
        ]);
    }

        /**
     * @Route("/topic/{id}/like", name="like_topic")
     * 
     *  @IsGranted("ROLE_USER")
     */
    public function like(LikeStorage $like = NULL, Topic $topic, EntityManagerInterface $manager)
    {
        if (!$like) {
            $like = New LikeStorage();
            $like->setUserId($this->getUser());
            $like->setTopicId($topic);
            $topic->addLike();
            $manager->persist($topic);
            $manager->persist($like);
            $manager->flush();
        } else {
            $topic->removeLike();
            dd('ici');

            $manager->persist($topic);
            $manager->remove($like);
            $manager->flush();
        }

        return $this->redirectToRoute('show_topic', ['id' => $topic->getId()]);
    }

    /**
     * @Route("/topic/{id}/dislike", name="dislike_topic")
     * 
     *  @IsGranted("ROLE_USER")
     */
    public function dislike(LikeStorage $like = NULL, Topic $topic, EntityManagerInterface $manager)
    {
        if (!$like) {
            $like = New LikeStorage();
            $like->setUserId($this->getUser());
            $like->setTopicId($topic);
            $topic->removeLike();
            $manager->persist($topic);
            $manager->persist($like);
            $manager->flush();
        } else {
            $topic->addLike();
            $manager->persist($topic);
            $manager->remove($like);
            $manager->flush();
        }

        return $this->redirectToRoute('show_topic', ['id' => $topic->getId()]);
    }

}
