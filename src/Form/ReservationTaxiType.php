<?php

namespace App\Form;

use App\Entity\ReservationTaxi;
use App\Entity\User;
use App\Entity\Vehicule;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationTaxiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, [
                'choices' => array_flip([
                    ReservationTaxi::STATUS_CONFIRMED => 'Confirmé',
                    ReservationTaxi::STATUS_PENDING => 'En attente',
                    ReservationTaxi::STATUS_REFUSED => 'Refusé',
                ]),
                'label' => 'Statut de la réservation',
            ])
            ->add('vehicule', EntityType::class, [
                'class' => Vehicule::class,
                'choice_label' => 'id',
                'label' => 'Véhicule',
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
                'label' => 'Utilisateur',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReservationTaxi::class,
        ]);
    }
}
