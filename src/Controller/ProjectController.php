<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Employee;
use App\Repository\ProjectRepository;
use App\Repository\EmployeeRepository;
use App\Form\ProjectType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/project')]
class ProjectController extends AbstractController
{
    #[Route('/', name: 'app_projects', methods: ['GET', 'POST'])]
    public function projects(ProjectRepository $repository): Response
    {
        $projects = $repository->findall();

        return $this->render('project/index.html.twig', [
            'controller_name' => 'ProjectController',
            'projects' => $projects,
        ]);
    }

    #[Route('/new', name: 'app_project_new', methods: ['GET', 'POST'])]
    #[Route('/edit/{id}', name: 'app_project_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function new(?Project $project, Request $request, EntityManagerInterface $manager ): Response
    {
        $project ??= new Project();
        
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() ){
            $manager->persist($project);
            $manager->flush();
            
            return $this->redirectToRoute('app_project', ['id' => $project->getId()]);
        }

        return $this->render('project/new.html.twig', [
            'controller_name' => 'ProjectController',
            'form' => $form,
        ]);
    }
    
    #[Route('/{id}', name: 'app_project', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function show(?Project $project): Response
    {
        return $this->render('project/project.html.twig', [
            'project' => $project,
        ]);
    }
    
    #[Route('/remove/{id}', name: 'app_project_remove', methods: ['GET', 'POST'])]
    public function remove(?Project $project, Request $request, EntityManagerInterface $manager ): Response
    {
        $manager->remove($project);
        $manager->flush();
            
            return $this->redirectToRoute('app_main');
    }
}
