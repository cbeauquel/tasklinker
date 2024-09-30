<?php

namespace App\Controller;

use App\Entity\Status;
use App\Entity\Project;
use App\Form\StatusType;
use App\Repository\StatusRepository;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Doctrine\ORM\EntityManagerInterface;


class StatusController extends AbstractController
{
    #[Route('/status/new/{projectId}', name: 'app_status_new', methods: ['GET' , 'POST'])]
    #[Route('/status/edit/{projectId}/{id}', name: 'app_status_edit', methods: ['GET' , 'POST'])]
    public function index(#[MapEntity(mapping: ['projectId' => 'id'])] ?Project $project, ?Status $status, Request $request, EntityManagerInterface $manager): Response
    {
        $status ??= new Status();
        $form = $this->createForm(StatusType::class, $status);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $project->addStatus($status);
            $manager->persist($status);
            $manager->flush();
            return $this->redirectToRoute('app_project', ['id' => $project->getId()]);
        }
        return $this->render('status/new.html.twig', [
            'controller_name' => 'StatusController',
            'form' => $form,
            'project' => $project,
        ]);
    }
}
