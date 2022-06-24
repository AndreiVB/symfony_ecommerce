<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'First name',
                'attr' => [
                    'placeholder' => 'Please enter your first name'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Last name',
                'attr' => [
                    'placeholder' => 'Please enter your last name'
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => 'Please enter your email adress'
                    ]
            ])
            ->add('password', PasswordType::class, [
                'attr' => [
                'placeholder' => 'Please enter your password'
                ]
            ])

            ->add('password_confirm', PasswordType::class, [
                'mapped' => false,
                'label'=> 'Confirm password',
                'attr' => [
                    'placeholder' => 'Please confirm your password'
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