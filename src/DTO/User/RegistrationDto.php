<?php

declare(strict_types=1);

namespace App\DTO\User;

use Symfony\Component\Validator\Constraints as Assert;

class RegistrationDto
{
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 180)]
    #[Assert\NoSuspiciousCharacters]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    #[Assert\PasswordStrength(minScore: Assert\PasswordStrength::STRENGTH_MEDIUM)]
    public string $plainPassword;

    #[Assert\NotBlank]
    #[Assert\EqualTo(propertyPath: 'plainPassword')]
    public string $passwordConfirmation;
}
