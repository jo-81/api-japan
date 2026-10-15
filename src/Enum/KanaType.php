<?php

declare(strict_types=1);

namespace App\Enum;

enum KanaType: string
{
    case HIRAGANA = 'hiragana';

    case KATAKANA = 'katakana';

    public function getLabel(): string
    {
        return match ($this) {
            self::HIRAGANA => 'Hiragana',
            self::KATAKANA => 'Katakana',
        };
    }

    /** @return string[] */
    public static function getChoices(): array
    {
        $choices = [];

        foreach (self::cases() as $case) {
            $choices[$case->getLabel()] = $case->name;
        }

        return $choices;
    }
}
