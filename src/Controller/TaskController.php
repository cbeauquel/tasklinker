<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\Status;
use App\Entity\Project;
use App\Form\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Doctrine\ORM\EntityManagerInterface;

class TaskController extends AbstractController
{
    #[Route('/task', name: 'app_task')]
    public function index(): Response
    {
        return $this->render('task/index.html.twig', [
            'controller_name' => 'TaskController',
        ]);
    }

    #[Route('/task/new/{projectId}', name: 'app_task_new', methods: ['GET' , 'POST'])]
    #[Route('/task/edit/{projectId}/{id}', name: 'app_task_edit', methods: ['GET' , 'POST'])]
    public function new(#[MapEntity(mapping: ['projectId' => 'id'])] ?Project $project, ?Task $task, Request $request, EntityManagerInterface $manager): Response
    {
        $task ??= new Task();
        $form = $this->createForm(TaskType::class, $task, [
            'project' => $project, // On passe l'objet projet au formulaire
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $project->addTask($task);
            $manager->persist($task);
            $manager->flush();
            return $this->redirectToRoute('app_project', ['id' => $project->getId()]);
        }
        return $this->render('task/new.html.twig', [
            'controller_name' => 'TaskController',
            'form' => $form,
            'project' => $project,
        ]);
    }
}
