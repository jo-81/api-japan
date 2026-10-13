<?php

declare(strict_types=1);

namespace App\DTO\ApiKey;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class ApiKeyDto
{
    public function __construct(
        #[Assert\NotBlank()]
        #[Assert\NoSuspiciousCharacters]
        #[Assert\Length(max: 255)]
        public ?string $name = null,

        public ?User $user = null,
    ) {
    }
}
