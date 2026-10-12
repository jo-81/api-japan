<?php

declare(strict_types=1);

namespace App\Enum;

enum ApiKeyStatus: string
{
    case ACTIVE = 'active';
    case COMPROMISED = 'compromised';
    case REVOKED = 'revoked';

    public function isUsable(): bool
    {
        return self::ACTIVE === $this;
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE => 'Actif',
            self::COMPROMISED => 'Compromis',
            self::REVOKED => 'Révoquée',
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
