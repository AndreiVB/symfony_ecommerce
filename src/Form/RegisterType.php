<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'First name',
                'attr' => [
                    'placeholder' => 'Please enter your first name'
                ],
                'constraints' => new Length([
                    'min' => 3,
                    'max' => 30
                    ])
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Last name',
                'attr' => [
                    'placeholder' => 'Please enter your last name'
                ],
                'constraints' => new Length([
                    'min' => 3,
                    'max' => 30
                    ])
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => 'Please enter your email address'
                ],
                'constraints' => new Length([
                    'min' => 3,
                    'max' => 50
                    ])
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Paswords must match!',
                'required' => true,
                'first_options' => [
                    'label' => 'Password',
                    'attr' => [
                        'placeholder' => 'Please enter your password'
                    ]

                ],
                'second_options' => [
                    'label' => 'Password confirm',
                    'attr' => [
                        'placeholder' => 'Please confirm your password'
                    ]
                    ]
                
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Register'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}