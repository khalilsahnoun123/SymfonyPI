<?php

namespace App\Form\transportpublic;

use App\Entity\transportpublic\Reservation;
use App\Entity\transportpublic\Station;
use App\Entity\transportpublic\Ligne;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationUserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Ligne $ligne */
        $ligne = $options['ligne'];
        $stations = $ligne->getStations();

        $builder
            ->add('travelDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de voyage',
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d'),
                ],
            ])
            ->add('ticketCategory', ChoiceType::class, [
                'choices' => [
                    'Économique' => 'ECONOMIC',
                    'Premium'    => 'PREMIUM',
                    'VIP'        => 'VIP',
                ],
                'label' => 'Catégorie de billet',
            ])
            ->add('numberOfSeats', IntegerType::class, [
                'label' => 'Nombre de sièges',
                'attr' => [
                    'min' => 1,
                ],
            ])
            ->add('departStation', EntityType::class, [
                'class'        => Station::class,
                'choices'      => $stations,
                'choice_label' => 'nom',
                'label'        => 'Station de départ',
                'placeholder'  => 'Sélectionnez une station',
            ])
            ->add('finStation', EntityType::class, [
                'class'        => Station::class,
                'choices'      => $stations,
                'choice_label' => 'nom',
                'label'        => 'Station d\'arrivée',
                'placeholder'  => 'Sélectionnez une station',
            ]);

        // Enforce same-line stations
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            $depart = $data->getDepartStation();
            $fin   = $data->getFinStation();

            if ($depart && $fin && $depart->getLigne()->getId() !== $fin->getLigne()->getId()) {
                $form->get('finStation')->addError(new FormError(
                    'La station d\'arrivée doit appartenir à la même ligne que la station de départ.'
                ));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'ligne'      => null,
        ]);

        $resolver->setRequired('ligne');
        $resolver->setAllowedTypes('ligne', Ligne::class);
    }
}
