<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class Kanji extends Constraint
{
    public string $message = 'La valeur "{{ value }}" doit contenir uniquement des kanji.';

    public function __construct(
        public bool $allowIterationMark = false,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(null, $groups, $payload);
    }
}
