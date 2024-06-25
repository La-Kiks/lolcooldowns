<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ChampionHasteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('champion', TextType::class, [
                'label' => 'Champion',
                'required' => false,
                'empty_data' => ''
            ])
            ->add('haste',  NumberType::class, [
                'label' => 'Haste',
                'required' => false,
                'scale' => 0
            ])
            ;
    }
}