<?php

namespace App\Form;

use App\Entity\Employee;
use App\Enum\ContractStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Serializer\Encoder\JsonEncode;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $arrayToSend = ['role_employee' => 'ROLE_EMPLOYEE'];
        $builder
        ->add('lastName', TextType::class, [
            'label' => 'employee.last_name',
        ])
        ->add('firstName', TextType::class, [
            'label' => 'employee.first_name',
        ])
        ->add('email')

        ->add('plainPassword',  RepeatedType::class,  [ 
            'type'=> PasswordType::class,
            // instead of being set onto the object directly,
            // this is read and encoded in the controller
            'mapped' => false,
            'attr' => ['autocomplete' => 'new-password'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a password',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Your password should be at least {{ limit }} characters',
                    // max length allowed by Symfony for security reasons
                    'max' => 4096,
                ]),
            ],
            'first_options' => ['label' => 'Mot de passe'],
            'second_options' => ['label' => 'Confirmer le mot de passe'],
            'invalid_message' => 'Les mots de passe ne correspondent pas',
        ])
        ->add('entryDate', DateType::class, [
            'widget' => 'single_text',
            'label' => 'employee.entry_date',
        ])
        ->add('contractType', EnumType::class, [
            'class' => ContractStatus::class,
            'label' => 'employee.contract_type',
        ])
        // ->add('roles', HiddenType::class, [
        //     'data' => json_encode($arrayToSend),
        // ])
        ;
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employee::class,
        ]);
    }
}
