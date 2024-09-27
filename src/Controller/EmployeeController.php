<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use App\Form\EmployeeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/employee')]
class EmployeeController extends AbstractController
{
    #[Route('/', name: 'app_employee')]
    public function index(EmployeeRepository $repository): Response
    {
        $employees = $repository->findall();

        return $this->render('employee/index.html.twig', [
            'controller_name' => 'EmployeeController',
            'employees' => $employees,
        ]);
    }

    #[Route('/new', name: 'app_employee_new', methods: ['GET', 'POST'])]
    #[Route('/edit/{id}', name: 'app_employee_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function new(?Employee $employee, Request $request, EntityManagerInterface $manager): Response
    {
        $employee ??= new Employee();
        
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() ){
            $manager->persist($employee);
            $manager->flush();
            
            return $this->redirectToRoute('app_employee');
        }

        return $this->render('employee/new.html.twig', [
            'controller_name' => 'employeeController',
            'form' => $form,
        ]);
    }
       
    #[Route('/remove/{id}', name: 'app_employee_remove', methods: ['GET', 'POST'])]
    public function remove(?Employee $employee, Request $request, EntityManagerInterface $manager ): Response
    {
        $manager->remove($employee);
        $manager->flush();
            
            return $this->redirectToRoute('app_employee');
    }
}
