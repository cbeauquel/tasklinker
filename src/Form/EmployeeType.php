<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\Project;
use App\Entity\Task;
use App\Enum\ContractStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class EmployeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('LastName', TextType::class)
            ->add('FirstName', TextType::class)
            ->add('Email', EmailType::class)
            ->add('EntryDate', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('ContractType', EnumType::class, [
                'class' => ContractStatus::class,
            ])
            ->add('projects', EntityType::class, [
                'class' => Project::class,
                'choice_label' => 'name',
                'required'   => false,
                'multiple' => true,
            ])
            ->add('tasks', EntityType::class, [
                'class' => Task::class,
                'choice_label' => 'id',
                'required'   => false,
                'multiple' => true,
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
