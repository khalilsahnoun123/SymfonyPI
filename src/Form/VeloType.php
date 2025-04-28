<?php
// src/Form/VeloType.php
namespace App\Form;

use App\Entity\Velo;
use App\Entity\VeloType as BikeType; // Renamed to avoid conflict with form class name
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType; // Add this import

class VeloType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dispo', CheckboxType::class, [
                'label' => 'Available',
                'required' => false
            ])
            ->add('veloType', EntityType::class, [
                'class' => BikeType::class, // Using the renamed alias
                'choice_label' => 'typeName',
                'label' => 'Bike Type',
                'placeholder' => 'Select a bike type' // Optional
            ])
            // Remove the id_type field if you're using the veloType relationship
            // ->add('id_type', IntegerType::class, [
            //    'label' => 'Type ID'
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Velo::class,
        ]);
    }
}