<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'disabled' => true,
                'label' => 'My email'
            ])
            ->add('firstname', TextType::class, [
                'disabled' => true,
                'label' => 'First name'
            ])
            ->add('lastname', TextType::class, [
                'disabled' => true,
                'label' => 'Last name'
            ])
            ->add('old_password', PasswordType::class, [
                'mapped' => false,
                'label' => 'Current password',
                'attr' => [
                    'placeholder' => 'Enter your current password'
                ]
            ])
            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'Paswords must match!',
                'label' => 'New password',
                'required' => true,
                'first_options' => [
                    'label' => 'New password',
                    'attr' => [
                        'placeholder' => 'Please enter your new password'
                    ]

                ],
                'second_options' => [
                    'label' => 'Password confirm',
                    'attr' => [
                        'placeholder' => 'Please confirm your new password'
                    ]
                    ]
                
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Change password'
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