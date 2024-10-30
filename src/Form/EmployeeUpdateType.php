<?php

namespace App\Form;

use App\Entity\Employee;
use App\Enum\ContractStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class EmployeeUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
            $builder
                ->add('lastName', TextType::class, [
                    'label' => 'employee.last_name',
                ])
                ->add('firstName', TextType::class, [
                    'label' => 'employee.first_name',
                ])
                ->add('email', EmailType::class, [
                    'label' => 'employee.email',
                ])
                ->add('entryDate', DateType::class, [
                    'widget' => 'single_text',
                    'label' => 'employee.entry_date',
                ])
                ->add('contractType', EnumType::class, [
                    'class' => ContractStatus::class,
                    'label' => 'employee.contract_type',
                ])
                ->add('roles', ChoiceType::class, [
                    'choices' => [
                        'ProjectManager' => 'ROLE_PROJECT_MANAGER',
                        'Employee' => 'ROLE_EMPLOYEE',
                    ],
                    'expanded' => true,
                    'multiple' => true,
                    'label' => 'employee.roles',
                ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employee::class,
        ]);
    }
}
