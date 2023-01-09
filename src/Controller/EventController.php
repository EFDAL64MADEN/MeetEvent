<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Event;
use App\Entity\Comment;
use App\Entity\Picture;
use App\Form\EventType;
use App\Form\CommentType;
use App\Form\SearchEventType;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class EventController extends AbstractController
{
    /**
     * @Route("/profile/event", name="app_event")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        // récupère tous les évènements de la bdd
        $events = $doctrine->getRepository(Event::class)->findBy([], ["startEvent" => "ASC"]);

        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }

    /**
     * @Route("/profile/search", name="app_search")
     */
    public function search(ManagerRegistry $doctrine, Request $request): Response
    {
        // On récupère les évènements, dans l'ordre croissant des dates de début
        $events = $doctrine->getRepository(Event::class)->findBy([], ["startEvent" => "ASC"]);
        // On crée le formulaire depuis le formtype destiné à la recherche
        $form = $this->createForm(SearchEventType::class);
        // La fonction handleRequest() permet de lire les données des superglobales PHP en fonction de la requête HTTP configurée sur le formulaire (POST par défaut)
        $search = $form->handleRequest($request);

        // Si le formulaire est soumis et validé
        if($form->isSubmitted() && $form->isValid()){
            // On recherche les évènements correspondant aux mots-clés
            $events = $doctrine->getRepository(Event::class)->search(
                $search->get('keyWord')->getData()
            );
    // On renvoie vers la vue des résultats de la recherche
    return $this->render('search/results.html.twig', [
        // La vue aura comme paramètre les résultats de la recherche
        'events' => $events
    ]);
        }
        // On renvoie vers la vue du formulaire de recherche
        return $this->render('search/index.html.twig', [
            // La vue aura comme paramètre le formualaire
            'searchForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/profile/event/add", name="add_event")
     */
    public function addEvent(ManagerRegistry $doctrine, Event $event = null, Request $request): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $event = $form->getData();
            $event->addUser($this->getUser());
            // Récupération des images transmises dans le formulaire
            $pictures = $form['pictures']->getData();
            // On boucle sur les images
            foreach($pictures as $picture){
                // On génère un nouveau nom d'image
                $file = md5(uniqid()).'.'.$picture->guessExtension();

                // On envoie le fichier vers le dossier uploads
                $picture->move(
                    $this->getParameter('event_images_directory'),
                    $file
                );

                // On stocke le nom d'image en bdd
                $pctr = new Picture();
                $pctr->setUrl($file);
                $event->addPicture($pctr);
            }

            // vérification pour être sur qu'un user est connecté
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            // on récupère le user de la session actuelle, ou null si il n'y en pas
            /** @var User $user */
            $user = $this->getUser();
            $event->setUser($user);
            $entityManager = $doctrine->getManager();
            // prepare
            $entityManager->persist($event);
            // insert into (execute)
            $entityManager->flush();

            return $this->redirectToRoute("app_event");

        }

        // renvoie vers la vue du formulaire d'ajout
        return $this->render('event/add.html.twig', [
            'formAddEvent' => $form->createView(),
            'edit' => false,
            'event' => $event
        ]);
    }

    /**
     * @Route("/profile/event/edit/{id}", name="edit_event")
     */
    public function editEvent(ManagerRegistry $doctrine, Event $event = null, Request $request): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $event = $form->getData();
            // Récupération des images transmises dans le formulaire
            $pictures = $form['pictures']->getData();
            // On boucle sur les images
            foreach($pictures as $picture){
                // On génère un nouveau nom d'image
                $file = md5(uniqid()).'.'.$picture->guessExtension();

                // On envoie le fichier vers le dossier uploads
                $picture->move(
                    $this->getParameter('event_images_directory'),
                    $file
                );

                // On stocke le nom d'image en bdd
                $pctr = new Picture();
                $pctr->setUrl($file);
                $event->addPicture($pctr);
            }

            $entityManager = $doctrine->getManager();
            // prepare
            $entityManager->persist($event);
            // insert into (execute)
            $entityManager->flush();

            return $this->redirectToRoute("details_event", ["id" => $event->getId()]);

        }

        // renvoie vers la vue du formulaire d'ajout
        return $this->render('event/add.html.twig', [
            'formAddEvent' => $form->createView(),
            'edit' => true,
            'event' => $event
        ]);
    }

    /**
     * @Route("/profile/event/{id}", name="details_event")
     */
    public function detailsEvent(Event $event): Response
    {
        return $this->render('event/details.html.twig', [
            'event' => $event,
        ]);
    }

    /**
     * fonction pour participer à un évènement
     * @Route("/profile/event/{event_id}/user/participate/{user_id}", name="app_participate")
     * @ParamConverter("event", options={"id" = "event_id"})
     * @ParamConverter("user", options={"id" = "user_id"})
     */
    public function participate(ManagerRegistry $doctrine, Event $event, User $user)
    {
        // Ajouter l'utilisateur au tabeau des participants de l'évènement
        $event->addUser($user);
        // Faire appel à l'entityManager via Doctrine pour manipuler des objets
        $entityManager = $doctrine->getManager();
        // On prévient Doctrine que l'on va potentiellement insérer des données en base
        $entityManager->persist($event);
        // On exécute la requête (on insère les données)
        $entityManager->flush();
        // Permet de prévenir l'utilisateur que l'opération a fonctionné
        $this->addFlash('success', 'Vous êtes bien inscrits à l\'évènement');
    }

    /**
     * @Route("/profile/event/{event_id}/user/unsubscribe/{user_id}", name="app_unsubscribe")
     * @ParamConverter("event", options={"id" = "event_id"})
     * @ParamConverter("user", options={"id" = "user_id"})
     */
    public function unsubscribe(ManagerRegistry $doctrine, Event $event, User $user)
    {
        $event->removeUser($user);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($event);
        $entityManager->flush();
        $this->addFlash('success', 'Vous vous êtes désinscrits de l\'évènement');
        return $this->redirectToRoute('app_event');
    }

    /**
     * @Route("/profile/event/see/{id}", name="see_past_event")
     */
    public function seePastEvent(Event $event, Request $request, ManagerRegistry $doctrine): Response
    {
        $comment = new Comment;

        // On génère le formulaire
        $form = $this->createForm(CommentType::class, $comment);
        // La fonction handleRequest() permet de lire les données des superglobales PHP en fonction de la requête HTTP configurée sur le formulaire (POST par défaut)
        $form->handleRequest($request);

        // On traîte le formulaire
        if($form->isSubmitted() && $form->isValid()){

            // On définit l'évènement qu'on va commenter
            $comment->setEvent($event);
            // On définit le User qui commente
            $comment->setUser($this->getUser());

            // On récupère l'EntityManager via Doctrine : permet de manipuler les entités
            $entityManager = $doctrine->getManager();
            // on récupère l'objet comment mais on n'envoie rien
            $entityManager->persist($comment);
            // on envoie tout en bdd (possibilité de faire plusieurs persist avant de flush)
            $entityManager->flush();

            $this->addFlash('message', 'Commentaire ajouté');
        }

        // Renvoie la vue de l'évènement passé, avec le formulaire de commentaire
        return $this->render('event/past.html.twig', [
            'formComment' => $form->createView(),
            'event' => $event,
        ]);
    }

    /**
     * @Route("/profile/delete/picture/{id}", name="event_delete_picture", methods={"DELETE"})
     */
    public function deletePicture(Picture $picture, ManagerRegistry $doctrine, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        
        // On vérifie si le token et valide
        if($this->isCsrfTokenValid('delete'.$picture->getId(), $data['_token'])){
            // On récupère le nom de l'image
            $name = $picture->getUrl();
            // On supprime le fichier
            unlink($this->getParameter('event_images_directory').'/'.$name);

            // On supprime l'entrée de la base
            $entityManager = $doctrine->getManager();
            $entityManager->remove($picture);
            $entityManager->flush();

            // On répond en json
            return new JsonResponse(['success' => 1]);
        } else{
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }

    /**
     * @Route("/profile/event/delete/{id}", name="delete_event")
     */
    public function deleteEvent(ManagerRegistry $doctrine, Event $event): Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($event);
        $entityManager->flush();
        return $this->redirectToRoute('app_event');
    }
}
