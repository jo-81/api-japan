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
}
