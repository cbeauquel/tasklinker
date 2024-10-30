<?php

namespace App\Controller\Restricted;

use App\Entity\Task;
use App\Entity\Status;
use App\Form\TaskType;
use App\Entity\Project;
use App\Form\EmployeeTaskType;
use Doctrine\ORM\EntityManagerInterface;
use App\Security\Voter\EmployeeTaskVoter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/restricted/task')]
class TaskController extends AbstractController
{
    #[IsGranted('ROLE_PROJECT_MANAGER')]
    #[Route('/new/{projectId}', name: 'app_task_new', requirements: ['id' => '\d+'], methods: ['GET' , 'POST'])]
    #[Route('/edit/{projectId}/{id}', name: 'app_task_edit', requirements: ['id' => '\d+'], methods: ['GET' , 'POST'])]
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
        return $this->render('restricted/task/new.html.twig', [
            'controller_name' => 'TaskController',
            'form' => $form,
            'task' => $task,
            'project' => $project,
        ]);
    }

    #[IsGranted(EmployeeTaskVoter::EDIT, subject: 'task')]
    #[Route('/update/{projectId}/{id}', name: 'app_task_update', requirements: ['id' => '\d+'], methods: ['GET' , 'POST'])]
    public function update(#[MapEntity(mapping: ['projectId' => 'id'])] ?Project $project, ?Task $task, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(EmployeeTaskType::class, $task, [
            'project' => $project, // On passe l'objet projet au formulaire
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($task);
            $manager->flush();
            return $this->redirectToRoute('app_project', ['id' => $project->getId()]);
        }
        return $this->render('restricted/task/new.html.twig', [
            'controller_name' => 'TaskController',
            'form' => $form,
            'task' => $task,
            'project' => $project,
        ]);
    }
   
    #[IsGranted('ROLE_PROJECT_MANAGER')]
    #[Route('/remove/{projectId}/{id}', name: 'app_task_remove', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function remove(?Task $task, EntityManagerInterface $manager, #[MapEntity(mapping: ['projectId' => 'id'])] ?Project $project): Response
    {
        $manager->remove($task);
        $manager->flush();
            
            return $this->redirectToRoute('app_project', ['id' => $project->getId()]);
    }
}
