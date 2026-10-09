<?php

declare(strict_types=1);

namespace App\DTO\User;

use Symfony\Component\Validator\Constraints as Assert;

class RegistrationDto
{
    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide.')]
    #[Assert\Email(message: 'Cette valeur doit être une adresse email valide.')]
    #[Assert\Length(max: 180)]
    #[Assert\NoSuspiciousCharacters]
    public string $email;

    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide.')]
    #[Assert\Length(min: 8)]
    #[Assert\PasswordStrength(minScore: Assert\PasswordStrength::STRENGTH_MEDIUM)]
    public string $plainPassword;

    #[Assert\NotBlank]
    #[Assert\EqualTo(propertyPath: 'plainPassword', message: 'Les mots de passe ne sont pas identiques.')]
    public string $passwordConfirmation;
}
