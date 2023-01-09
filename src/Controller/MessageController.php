<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    /**
     * @Route("/profile/message", name="app_message")
     */
    public function index(): Response
    {
        return $this->render('message/index.html.twig');
    }

    /**
     * @Route("/profile/send", name="send_message")
     */
    public function sendMessage(Request $request, ManagerRegistry $doctrine): Response
    {
        // On récupère l'id de l'utilisateur en session, qui va être l'envoyeur
        $id = $this->getUser()->getId();
        $message = new Message;
        // On rajoute une option qui sera l'id de l'utilisateur qui servira à la requête DQL dans le form
        $form = $this->createForm(MessageType::class, $message, ['id' => $id]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $message->setSender($this->getUser());

            $em = $doctrine->getManager();
            $em->persist($message);
            $em->flush();

            $this->addFlash('sent', 'Message envoyé');
            return $this->redirectToRoute('app_message');
        }

        return $this->render("message/add.html.twig", [
            "formMessage" => $form->createView(),
        ]);
    }

    /**
     * @Route("/profile/received", name="app_received")
     */
    public function receivedMessage(): Response
    {
        return $this->render('message/received.html.twig');
    }

    /**
     * @Route("/profile/sent", name="app_sent")
     */
    public function sentMessage(): Response
    {
        return $this->render('message/sent.html.twig');
    }

    /**
     * @Route("/profile/read/{id}", name="app_read")
     */
    public function readMessage(Message $message, ManagerRegistry $doctrine): Response
    {
        $message->setIsRead(true);
        $em = $doctrine->getManager();
        $em->persist($message);
        $em->flush();

        return $this->render('message/read.html.twig', compact('message'));
    }

    /**
     * @Route("/profile/readsent/{id}", name="app_read_sent")
     */
    public function readSentMessage(Message $message): Response
    {
        return $this->render('message/readsent.html.twig', compact('message'));
    }

    /**
     * @Route("/profile/delete/{id}", name="delete_message")
     */
    public function deleteMessage(Message $message, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $em->remove($message);
        $em->flush();

        return $this->redirectToRoute("app_received");
    }
}
