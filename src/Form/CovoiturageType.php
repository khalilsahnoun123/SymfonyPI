<?php

namespace App\Form;

use App\Entity\Covoiturage;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CovoiturageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('point_de_depart', TextType::class, [
                'label' => 'Point de départ',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Lieu de départ'
                ]
            ])
            ->add('point_de_destination', TextType::class, [
                'label' => 'Point de destination',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Lieu d\'arrivée'
                ]
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Prix',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Prix en euros'
                ]
            ])
            ->add('nbr_place', NumberType::class, [
                'label' => 'Nombre de places',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Nombre de places disponible'
                ]
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
                'label' => 'utilisateur',
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Covoiturage::class,
        ]);
    }
}