<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\SearchEventType;
use App\Repository\EventRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    // /**
    //  * @Route("/profile/results", name="app_results")
    //  */
    // public function showResults()
    // {
    //     return $this->render('search/results.html.twig', [
    //         'mots' => $mots
    //     ]);
    // }

    
}
