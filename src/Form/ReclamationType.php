<?php

namespace App\Form;

use App\Entity\Reclamation;
use App\Entity\TypeReclamation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description')

            
            ->add('type', EntityType::class, [
                'class' => TypeReclamation::class,
                'choice_label' => 'libelle',
                'placeholder' => 'veiller selectionner un type',
                'required' => true,
                'label' => 'TypeReclamation',

            ])
        
            ->add('attachment', FileType::class, [
                'label' => 'Pièce jointe (image, PDF…)',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/*',
                            'application/pdf',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un fichier valide (image ou PDF).',
                    ])
                ],
            ])


        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
