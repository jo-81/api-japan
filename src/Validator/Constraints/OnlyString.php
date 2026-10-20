<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class OnlyString extends Constraint
{
    public string $message = 'La valeur "{{ value }}" doit être une chaine de caractères, elle ne peut pas contenir des kana et/ou des kanji.';

    public function __construct(
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(null, $groups, $payload);
    }
}
