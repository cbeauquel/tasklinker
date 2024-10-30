<?php

namespace App\Form;

use App\Entity\Status;
use App\Entity\Task;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeeTaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $project = $options['project'];

        $builder
            ->add('status', EntityType::class, [
                'class' => Status::class,
                'choices' => $project->getStatuses(),
                'choice_label' => 'value',
                'multiple' => false,
                'label' => 'task.status',
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
