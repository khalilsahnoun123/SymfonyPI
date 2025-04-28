<?php

namespace App\Form;

use App\Entity\Reservationvelo;
use App\Entity\User;
use App\Entity\Velo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationveloType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_debut', null, [
                'widget' => 'single_text',
            ])
            ->add('date_fin', null, [
                'widget' => 'single_text',
            ])
            
            ->add('price', NumberType::class, [
                'scale' => 2, // Ensures the price is formatted with 2 decimal places
                'attr' => [
                    'placeholder' => 'Enter the price',
                ],
            ])
            
           
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('velo', EntityType::class, [
                'class' => Velo::class,
                'choice_label' => 'id_velo',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservationvelo::class,
        ]);
    }
}
