<?php

namespace App\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class ResetPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('plainPassword', PasswordType::class, [
            'label' => 'New Password',
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 6])
            ],
            'attr' => ['class' => 'form-control']
        ]);
    }
}
