<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Reclamation;

class EvaluationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('note', ChoiceType::class, [
                'choices' => [5 => 5, 4 => 4, 3 => 3, 2 => 2, 1 => 1],
                'expanded' => true,
                'label' => 'Note (1 à 5 étoiles)',
                'required' => true,
            ])
            ->add('commentaireSatisfaction', TextareaType::class, [
                'label' => 'Commentaire de satisfaction',
                'required' => false,
                'attr' => ['rows' => 4],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Reclamation::class]);
    }
}
