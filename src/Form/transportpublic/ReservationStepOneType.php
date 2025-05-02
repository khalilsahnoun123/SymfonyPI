<?php
// src/Form/ReservationStepOneType.php
namespace App\Form\transportpublic;

use App\Entity\transportpublic\Ligne;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class ReservationStepOneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('travel_date', DateType::class, [
                'widget' => 'single_text',
                'label'  => 'Date de voyage',
            ])
            ->add('ligne', EntityType::class, [
                'class'        => Ligne::class,
                'choice_label' => 'name',
                'placeholder'  => 'Choisissez une ligne',
                'mapped'       => false,
                'label'        => 'Ligne',
            ])
            ->add('ticket_category', ChoiceType::class, [
                'choices' => [
                    'Économique' => 'ECONOMIC',
                    'Premium'    => 'PREMIUM',
                    'VIP'        => 'VIP',
                ],
                'label' => 'Catégorie',
            ])
            ->add('vehicle_type', ChoiceType::class, [
                'choices'  => ['Métro' => 'METRO', 'Bus' => 'BUS'],
                'mapped'   => false,
                'label'    => 'Type de véhicule',
                'required' => true,
            ])
        ;
    }
}
