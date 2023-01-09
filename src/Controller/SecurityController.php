<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/profile/account", name="app_account")
     */
    public function myAccount(): Response
    {
        return $this->render('security/account.html.twig');
    }

    /**
     * @Route("/profile/password/edit", name="edit_password")
     */
    public function editPassword(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordEncoder)
    {       
        if($request->isMethod('POST')){
            $entityManager = $doctrine->getManager();
            $user = $this->getUser();
            // On vérifie que les 2 mots de passe correspondent
            if($request->request->get('inputPassword5') == $request->request->get('inputPassword52')){
                $user->setPassword($passwordEncoder->hashPassword($user, $request->request->get('inputPassword5')));
                $entityManager->flush();
                $this->addFlash('message', 'Mots de passe mis à jour !');
                return $this->redirectToRoute('app_account');
            } else{
                $this->addFlash('errorPassword', 'Les deux mots de passes ne correspondent pas !');
            }
        }

        return $this->render('security/editpassword.html.twig');
    }

    /**
     * @Route("/profile/user/{id}/delete", name="delete_account")
     */
    public function deleteAccount($id, ManagerRegistry $doctrine)
    {       
        $currentUserId = $this->getUser()->getId();
        if ($currentUserId == $id)
        {
            $session = new Session();
            $session->invalidate();
        }
        
        $this->addFlash('sup', 'Votre compte a bien été supprimé');

        $em = $doctrine->getManager();
        $usrRepo = $em->getRepository(User::class);

        $user = $usrRepo->find($id);
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('app_home');
    }

//     /**
//      * @Route("/profile/password/edit/{id}", name="edit_password")
//      */
//     public function editPassword(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordEncoder, User $user = null)
//     {
//         // $user = $this->getUser();
//         if($request->isMethod('POST')){
//             $entityManager = $doctrine->getManager();

//             // On vérifie que les 2 mots de passe correspondent
//             if($request->request->get('inputPassword5') == $request->request->get('inputPassword52')){
                
//                 $user->setPassword($passwordEncoder->hashPassword($user, $request->request->get('inputPassword5')));
//                 $entityManager->flush();
//                 return $this->redirectToRoute('app_account');
//             } else{
//                 $this->addFlash('errorPassword', 'Les deux mots de passes ne correspondent pas !');
//             }
//         }

//         return $this->render('security/editpassword.html.twig');
//     }
}
