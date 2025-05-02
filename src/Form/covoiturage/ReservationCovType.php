<?php

namespace App\Form\covoiturage;

use App\Entity\covoiturage\ReservationCov;
use App\Entity\covoiturage\User;
use App\Entity\covoiturage\Covoiturage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationCovType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Confirmé' => 'confirmé',
                    'En attente' => 'en_attente',
                    'Annulé' => 'annulé',
                ],
                'label' => 'Statut de la réservation',
            ])
            ->add('nbrPlace', IntegerType::class, [
                'label' => 'Nombre de places',
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',  // Choix de l'utilisateur par son ID
                'label' => 'Utilisateur',
            ])
            ->add('covoiturage', EntityType::class, [
                'class' => Covoiturage::class,
                'choice_label' => 'id',  // Choix du covoiturage par son ID
                'label' => 'Covoiturage',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReservationCov::class,
        ]);
    }
}
