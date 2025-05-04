<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\File;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom est requis']),
                    new Assert\Length(['min' => 2, 'max' => 100, 'minMessage' => 'Le nom doit contenir au moins 2 caractères']),
                ]
            ])
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le prénom est requis']),
                    new Assert\Length(['min' => 2, 'max' => 100, 'minMessage' => 'Le prénom doit contenir au moins 2 caractères']),
                ]
            ])
            ->add('lastName', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => 100, 'maxMessage' => 'Le nom de famille ne peut pas dépasser 100 caractères']),
                ]
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Email requis']),
                    new Assert\Email(['message' => 'Email non valide']),
                ]
            ])
            ->add('gouvernorat', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => 255, 'maxMessage' => 'Le gouvernorat ne peut pas dépasser 255 caractères']),
                ]
            ])
            ->add('municipalite', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => 255, 'maxMessage' => 'La municipalité ne peut pas dépasser 255 caractères']),
                ]
            ])
            ->add('adresse', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => 255, 'maxMessage' => 'L\'adresse ne peut pas dépasser 255 caractères']),
                ]
            ])
            ->add('phoneNumber', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^[0-9]{8}$/',
                        'message' => 'Numéro invalide (8 chiffres requis)',
                    ])
                ]
            ])
            ->add('password', PasswordType::class, [
                'required' => false,
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new Assert\Length([
                        'min' => 6,
                        'max' => 255,
                        'minMessage' => 'Le mot de passe doit contenir au moins 6 caractères',
                        'maxMessage' => 'Le mot de passe ne peut pas dépasser 255 caractères'
                    ]),
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôles',
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Covoitureur' => 'ROLE_COVOITUREUR',
                    'Chauffeur Taxi' => 'ROLE_CHAUFFEUR_TAXI',
                    'Voyageur' => 'ROLE_VOYAGEUR',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'multiple' => true,
                'expanded' => true,
                'required' => true,
                'constraints' => [
                    new Assert\Count([
                        'min' => 1,
                        'minMessage' => 'Veuillez choisir au moins un rôle.',
                    ]),
                ]
            ])
            ->add('image', FileType::class, [
                'label' => 'Photo de profil (JPG, PNG, max 2MB)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPG, PNG ou WEBP)',
                        'maxSizeMessage' => 'L\'image ne peut pas dépasser 2MB'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
