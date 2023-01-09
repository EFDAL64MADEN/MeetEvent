<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditProfileType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;

class UserController extends AbstractController
{
    /**
     * @Route("/profile/account/edit", name="edit_profile")
     */
    public function editAccount(Request $request, ManagerRegistry $doctrine, UserRepository $ur)
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProfileType::class, $user);
        $form->handleRequest($request);
        $entityManager = $doctrine->getManager();

        if($form->isSubmitted() && $form->isValid()){
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['profilePicture']->getData();

            if($uploadedFile){            
                $newFileName = $ur->uploadProfilePicture($uploadedFile);
                $user->setProfilePicture($newFileName);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('message', 'Profil mis à jour');
            return $this->redirectToRoute('app_account');
        }

        return $this->render('security/editprofile.html.twig', [
            'formEditProfile' => $form->createView()
        ]);
    }

    /**
     * @Route("/profile/user/follow/{id}", name="app_follow")
     */
    public function follow(User $user, ManagerRegistry $doctrine)
    {
        // On récupère l'entityManager via Doctrine
        $em = $doctrine->getManager();
        // On définit le user en session
        $userFollowing = $this->getUser();
        // Dans le tableau de followers du user suivi, on ajoute le user en session
        $user->addFollower($userFollowing);
        // Dans le tableau de followers du user en session, on ajoute le user suivi
        $userFollowing->addFollower($user);
        // On persist l'user en session
        $em->persist($user);
        // On persist l'user suivi
        $em->persist($userFollowing);
        // On envoie les données en base
        $em->flush();
        $this->addFlash('message', 'Vous suivez désormais cet utilisateur');
    }

    /**
     * @Route("/profile/user/unfollow/{id}", name="app_unfollow")
     */
    public function unfollow(User $user, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $userFollowing = $this->getUser();
        $user->removeFollower($userFollowing);
        $userFollowing->removeFollower($user);
        $em->flush();
        $this->addFlash('message', 'Vous ne suivez plus cet utilisateur');

        return $this->render('security/account.html.twig');
    }

    /**
     * @Route("/profile/myFriends", name="see_friends")
     */
    public function seeFriends(): Response
    {
        // On retourne la vue à afficher
        return $this->render('user/followers.html.twig');
    }

    /**
     * @Route("/profile/user/{id}/seeEvents", name="see_events_of")
     */
    public function seeEventsOf(User $user): Response
    {
        // On récupère les évènements de l'utilisateur dont on a choisi de voir les évènements
        $events = $user->getEvents();
        // On retourne la vue à afficher
        return $this->render('user/events_of.html.twig', [
            'events' => $events,
            'friend' => $user
        ]);
    }

}
