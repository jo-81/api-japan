<?php

declare(strict_types=1);

namespace App\Enum;

enum KanaAccentType: string
{
    case DAKUTEN = 'dakuten';

    case HANDAKUTEN = 'handakuten';

    public function getLabel(): string
    {
        return match ($this) {
            self::DAKUTEN => 'Dakuten',
            self::HANDAKUTEN => 'Handakuten',
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
