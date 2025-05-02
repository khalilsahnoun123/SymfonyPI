<?php
namespace App\Form\transportpublic;

use App\Entity\transportpublic\Vehicules;
use App\Entity\transportpublic\Ligne;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
class VehiculesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Metro' => 'METRO',
                    'Bus' => 'BUS',
                 
                ],
                
                'label' => 'type vehicule',
            ]
            )
            ->add('nbr_max_passagers_vip')
            ->add('nbr_max_passagers_premium')
            ->add('nbr_max_passagers_economy')
            ->add('places_disponibles_vip')
            ->add('places_disponibles_premium')
            ->add('places_disponibles_economy')
            ->add('ligne', EntityType::class, [
                'class' => Ligne::class,
                'choice_label' => 'name',
                'label' => 'Ligne',
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicules::class,
        ]);
    }
}
