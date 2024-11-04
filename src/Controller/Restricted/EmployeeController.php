<?php

namespace App\Controller\Restricted;

use App\Entity\Employee;
use App\Form\EmployeeType;
use App\Form\EmployeeUpdateType;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;

#[IsGranted('ROLE_PROJECT_MANAGER')]
#[Route('/restricted/employee')]
class EmployeeController extends AbstractController
{
    #[Route('/', name: 'app_employee')]
    public function index(EmployeeRepository $repository): Response
    {
        $employees = $repository->findall();
        // dd($employees);
        return $this->render('restricted/employee/index.html.twig', [
            'controller_name' => 'EmployeeController',
            'employees' => $employees,
        ]);
    }

    #[Route('/new', name: 'app_employee_new', methods: ['GET', 'POST'])]
    public function new(?Employee $employee, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $manager): Response
    {
        $employee == new Employee();
        
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() ){
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $employee->setPassword($userPasswordHasher->hashPassword($employee, $plainPassword));
            $roles = $form->get('roles')->getData();
            $employee->setRoles($roles);

            $manager->persist($employee);
            $manager->flush();
            
            return $this->redirectToRoute('app_employee');
        }

        return $this->render('restricted/employee/new.html.twig', [
            'controller_name' => 'employeeController',
            'form' => $form,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_employee_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(?Employee $employee, Request $request, EntityManagerInterface $manager, GoogleAuthenticatorInterface $googleAuth): Response
    {       
        $form = $this->createForm(EmployeeUpdateType::class, $employee);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() ){
            $roles = $form->get('roles')->getData();
            $employee->setRoles($roles);
            $employee->setGoogleAuthenticatorSecret($googleAuth->generateSecret());

            $manager->persist($employee);
            $manager->flush();
            
            return $this->redirectToRoute('app_employee');
        }

        return $this->render('restricted/employee/edit.html.twig', [
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
