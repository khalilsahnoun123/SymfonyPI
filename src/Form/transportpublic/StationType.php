<?php

namespace App\Form\transportpublic;

use App\Entity\transportpublic\Station;
use App\Entity\transportpublic\Ligne;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('adresse')
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
            'data_class' => Station::class,
        ]);
    }
}
