<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Your first name',
                'attr' => [
                    'placeholder' => 'Please enter your first name'
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Your last name',
                'attr' => [
                    'placeholder' => 'Please enter your last name'
                ]              
            ])
            ->add('email', EmailType::class, [
                'label' => 'Your email',
                'attr' => [
                    'placeholder' => 'Please enter your email address'
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Your message',
                'attr' => [
                    'placeholder' => 'Please write your message here'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Send message',
                'attr' => [
                    'class' => 'btn-block btn-success'                  
                ]
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}