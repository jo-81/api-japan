<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class KanaIdeogramCode extends Constraint
{
    public string $message = 'La valeur "{{ value }}" doit contenir uniquement des kana (hiragana ou katakana).';

    public function __construct(
        public bool $allowProlongedSoundMark = false,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(null, $groups, $payload);
    }
}
