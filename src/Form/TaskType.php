<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\Project;
use App\Entity\Status;
use App\Entity\Task;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\Date;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $project = $options['project'];

        $builder
            ->add('Name', TextType::class)
            ->add('Description', TextareaType::class)
            ->add('StartDate', null, [
                'widget' => 'single_text',
            ])
            ->add('Employee', EntityType::class, [
                'class' => Employee::class,
                'choices' => $project->getEmployee(),
                'choice_label' => 'firstName',
                'multiple' => false,
            ])
            ->add('status', EntityType::class, [
                'class' => Status::class,
                'choices' => $project->getStatuses(),
                'choice_label' => 'value',
                'multiple' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);

        // On ajoute l'option `project` pour passer le projet dans le formulaire
        $resolver->setRequired('project');
    }
}
