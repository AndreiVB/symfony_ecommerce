<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name of your address',
                'attr' => [
                    'placeholder' => 'Please add an address name'
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Your first name',
                'attr' => [
                    'placeholder' => 'Please enter your first name'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Your last name',
                'attr' => [
                    'placeholder' => 'Please enter your last name'
                ]
            ])
            ->add('company', TextType::class, [
                'label' => 'Company name',
                'required' => false,
                'attr' => [
                    'placeholder' => '(optional)Please enter your company name'
                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'Name of your address',
                'attr' => [
                    'placeholder' => 'eg: Fleet Street, nr 8, London'
                ]
            ])
            ->add('postalcode', TextType::class, [
                'label' => 'Postal colde',
                'attr' => [
                    'placeholder' => 'Please enter your postal code'
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'City',
                'attr' => [
                    'placeholder' => 'Please enter your city name'
                ]
            ])
            ->add('country', CountryType::class, [
                'label' => 'Country',
                'attr' => [
                    'placeholder' => 'Please enter your contry name',
                    'class' => 'form-control'
                ]
            ])
            ->add('phone', TelType::class, [
                'label' => 'Phone',
                'attr' => [
                    'placeholder' => 'Please enter your phone number'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Save address',
                'attr' => [
                    'class' => 'btn btn-block btn-info'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}