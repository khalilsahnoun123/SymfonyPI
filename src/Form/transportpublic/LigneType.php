<?php

namespace App\Form\transportpublic;

use App\Entity\transportpublic\Ligne;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LigneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('prix_vip')
            ->add('prix_premium')
            ->add('prix_economique')
            ->add('region')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ligne::class,
        ]);
    }
}
