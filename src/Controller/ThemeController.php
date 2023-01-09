<?php

namespace App\Controller;

use App\Entity\Theme;
use App\Form\ThemeType;
use App\Repository\ThemeRepository;
use App\Service\UploaderHelper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ThemeController extends AbstractController
{
    /**
     * @Route("/profile/theme", name="app_theme")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $themes = $doctrine->getRepository(Theme::class)->findBy([], ["themeName" => "ASC"]);

        return $this->render('theme/index.html.twig', [
            'themes' => $themes
        ]);
    }

    /**
     * fonction pour afficher les thèmes d'un seul évènement
     * @Route("/profile/theme/{id}", name="details_theme")
     */
    public function detailsTheme(Theme $theme, ThemeRepository $tr): Response
    {
        // On récupère les évènements du thème en question via une requête DQL
        $eventsTheme = $tr->findEventsOfTheme($theme->getId());

        // On retourne la vue à afficher, avec comme paramètre le thème et les évènements de ce dernier
        return $this->render('theme/details.html.twig', [
            'theme' => $theme,
            'eventsTheme' => $eventsTheme
        ]);
    }

    /**
     * @Route("/admin/theme/add", name="add_theme")
     */
    public function addTheme(ManagerRegistry $doctrine, Theme $theme = null, Request $request, ThemeRepository $themeRepository): Response
    {
        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);
        $entityManager = $doctrine->getManager();

        if($form->isSubmitted() && $form->isValid()){
            $theme = $form->getData();

            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['logoFile']->getData();

            if($uploadedFile){            
                $newFileName = $themeRepository->uploadThemeLogo($uploadedFile);
                $theme->setLogo($newFileName);
            }
            
            // prepare
            $entityManager->persist($theme);
            // insert into (execute)
            $entityManager->flush();

            return $this->redirectToRoute("app_theme");

        }

        // renvoie vers la vue du formulaire d'ajout
        return $this->render('theme/add.html.twig', [
            'formAddTheme' => $form->createView(),
            'edit' => false
        ]);
    }

    /**
     * @Route("/admin/theme/edit/{id}", name="edit_theme")
     */
    public function editTheme(ManagerRegistry $doctrine, Theme $theme = null, Request $request, ThemeRepository $themeRepository): Response
    {
        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);
        $entityManager = $doctrine->getManager();

        if($form->isSubmitted() && $form->isValid()){
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['logoFile']->getData();

            if($uploadedFile){            
                $newFileName = $themeRepository->uploadThemeLogo($uploadedFile);
                $theme->setLogo($newFileName);
            }

            $entityManager->persist($theme);
            $entityManager->flush();

            return $this->redirectToRoute('app_theme');
        }

        // vue pour afficher le formulaire
        return $this->render('theme/add.html.twig', [
            'formAddTheme' => $form->createView(),
            'edit' => True,
        ]);
    }

    /**
     * @Route("/admin/theme/{id}/delete", name="delete_theme")
     */
    public function delete(ManagerRegistry $doctrine, Theme $theme): Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($theme);
        $entityManager->flush();

        return $this->redirectToRoute('app_theme');
    }
}
