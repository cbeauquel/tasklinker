<?php

namespace App\Controller\Welcome;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/accueil', name: 'welcome_app_main')]
    public function index(): Response
    {
        if($this->getUser()){
            return $this->redirectToRoute('app_projects');
        }
        
        return $this->render('welcome/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
