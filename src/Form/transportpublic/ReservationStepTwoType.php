<?php
// src/Form/ReservationStepTwoType.php
namespace App\Form\transportpublic;

use App\Entity\transportpublic\Station;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class ReservationStepTwoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // $options['stations'] should be passed from controller
        $stations = $options['stations'] ?? [];

        $builder
            ->add('number_of_seats', IntegerType::class, [
                'label' => 'Nombre de passagers',
            ])
            ->add('depart_station', EntityType::class, [
                'class'    => Station::class,
                'choices'  => $stations,
                'choice_label' => 'nom',
                'label'    => 'Station de départ',
            ])
            ->add('fin_station', EntityType::class, [
                'class'    => Station::class,
                'choices'  => $stations,
                'choice_label' => 'nom',
                'label'    => 'Station d’arrivée',
            ])
        ;
    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'stations' => [],
        ]);
    }
}
