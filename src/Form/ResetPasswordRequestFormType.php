<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

/**
 * @extends AbstractType<mixed>
 */
class ResetPasswordRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'help' => 'Saisissez votre adresse e-mail et nous vous enverrons un lien pour réinitialiser votre mot de passe.',
                'label' => 'Adresse email',
                'attr' => ['autocomplete' => 'email'],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez saisir votre adresse e-mail.',
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
