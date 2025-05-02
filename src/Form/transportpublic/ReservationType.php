<?php
namespace App\Form\transportpublic;
// src/Form/ReservationType.php



use App\Entity\transportpublic\Reservation;
use App\Entity\transportpublic\Ligne;
use App\Entity\transportpublic\Station;
use App\Entity\transportpublic\Vehicules;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // unmapped ligne field to filter stations and vehicules
        $builder->add('ligne', EntityType::class, [
            'class' => Ligne::class,
            'choice_label' => 'name',
            'placeholder' => 'Sélectionnez une ligne',
            'mapped' => false,
            'required' => true,
            'label' => 'Ligne',
        ]);

        // static fields
        $builder
            ->add('travel_date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de voyage',
            ])
            ->add('number_of_seats', IntegerType::class, [
                'label' => 'Nombre de sièges',
            ])
            ->add('ticket_category', ChoiceType::class, [
                'choices' => [
                    'Économique' => 'ECONOMIC',
                    'Premium' => 'PREMIUM',
                    'VIP' => 'VIP',
                ],
                'label' => 'Catégorie de billet',
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Confirmé' => 'CONFIRMED',
                    'En attente' => 'PENDING',
                    'Annulé' => 'CANCELLED',
                ],
                'label' => 'Statut',
            ])
            ->add('depart_station', EntityType::class, [
                'class' => Station::class,
                'choice_label' => 'nom',
                'placeholder' => 'Station de départ',
                'required' => true,
                'label' => 'Départ',
            ])
            ->add('fin_station', EntityType::class, [
                'class' => Station::class,
                'choice_label' => 'nom',
                'placeholder' => 'Station d’arrivée',
                'required' => true,
                'label' => 'Arrivée',
            ])
            ->add('vehicule', EntityType::class, [
                'class' => Vehicules::class,
                'choice_label' => 'type',
                'placeholder' => 'Choisissez un véhicule',
                'required' => true,
                'label' => 'Véhicule',
            ]);


      
     
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
