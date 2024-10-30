<?php

namespace App\Controller\Restricted;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Security\Voter\EmployeeProjectVoter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/restricted/project')]
class ProjectController extends AbstractController
{
    #[IsGranted(EmployeeProjectVoter::LIST)]
    #[Route('/', name: 'app_projects')]
    public function projects(ProjectRepository $repository): Response
    {
            $employeeId = $this->getUser()->getId();
            $canListAll = $this->isGranted(EmployeeProjectVoter::LIST_ALL);
            $projects = $repository->findByEmployeeField($canListAll ? null : $employeeId);

        return $this->render('restricted/project/index.html.twig', [
            'controller_name' => 'ProjectController',
            'projects' => $projects,
        ]);
    }

    #[IsGranted('ROLE_PROJECT_MANAGER')]
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

        return $this->render('restricted/project/new.html.twig', [
            'controller_name' => 'ProjectController',
            'form' => $form,
        ]);
    }
    
    #[IsGranted(EmployeeProjectVoter::VIEW, subject: 'project')]
    #[Route('/{id}', name: 'app_project', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(?Project $project): Response
    {       
        return $this->render('restricted/project/project.html.twig', [
            'project' => $project,
        ]);
    }
    
    #[Route('/remove/{id}', name: 'app_project_remove', methods: ['GET', 'POST'])]
    public function remove(?Project $project, EntityManagerInterface $manager ): Response
    {
        $manager->remove($project);
        $manager->flush();
            
            return $this->redirectToRoute('app_main');
    }
}
