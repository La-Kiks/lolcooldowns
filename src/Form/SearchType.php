<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('championName', TextType::class, [
                'label' => 'Champion',
                'required' => false,
                'empty_data' => ''
            ])
            ->add('haste', NumberType::class, [
                'label' => 'Haste',
                'required' => false,
                'empty_data' => 0,
                'scale' => 0
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'method' => 'GET',
            'crsf_protection' => true
        ]);
    }
}
