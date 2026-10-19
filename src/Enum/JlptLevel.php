<?php

declare(strict_types=1);

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Contracts\Translation\TranslatableInterface;

enum JlptLevel: string implements TranslatableInterface
{
    case JLPT1 = 'jlpt 1';

    case JLPT2 = 'jlpt 2';

    case JLPT3 = 'jlpt 3';

    case JLPT4 = 'jlpt 4';

    case JLPT5 = 'jlpt 5';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans(
            match ($this) {
                self::JLPT1 => 'jlpt 1',
                self::JLPT2 => 'jlpt 2',
                self::JLPT3 => 'jlpt 3',
                self::JLPT4 => 'jlpt 4',
                self::JLPT5 => 'jlpt 5',
            },
            locale: $locale,
        );
    }
}
