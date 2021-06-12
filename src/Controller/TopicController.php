<?php

namespace App\Controller;

use App\Entity\Topic;
use App\Form\TopicType;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TopicController extends AbstractController
{
    /**
     * @Route("/topic", name="topic")
     */
    public function index(TopicRepository $repo): Response
    {

        $topics = $repo->findAll();

        return $this->render('topic/index.html.twig', [
            'controller_name' => 'TopicController',
            'topics' => $topics ? $topics : NULL,
        ]);
    }

    /**
     * @Route("/topic/new", name="topic_create")
     * @Route("/topic/{id}/edit", name="topic_edit")
     */
    public function form(Topic $topic = NULL, Request $request, EntityManagerInterface $manager)
    {

        if (!$topic) {
            $topic = new Topic();
        }

        $form = $this->createForm(TopicType::class, $topic);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $topic->setAuthor($this->getUser());
            if (!$topic->getId()) {
                $topic->setCreatedAt(new \DateTime());
            }

            $manager->persist($topic);
            $manager->flush();

            return $this->redirectToRoute('topic_show', ['id' => $topic->getId()]);
        }
        return $this->render('topic/create.html.twig', [
            'formTopic' => $form->createView(),
            'editMode' => $topic->getId() !== NULL,
        ]);
    }

    /**
     * @Route("/topic/{id}", name="topic_show")
     */
    public function show(Topic $topic)
    {

        return $this->render('topic/show.html.twig', [
            'topic' => $topic,
        ]);
    }

    /**
     * @Route("/topic/{id}/delete", name="topic_delete")
     */
    public function delete(Topic $topic, EntityManagerInterface $manager)
    {

        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || $this->getUser() === $topic->getAuthor()) {
            $manager->remove($topic);
            $manager->flush();
        }

        return $this->redirectToRoute('topic');
    }
}
