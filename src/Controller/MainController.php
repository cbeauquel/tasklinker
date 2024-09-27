<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/accueil', name: 'app_main', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        return $this->redirectToRoute('app_projects');
    }
}
