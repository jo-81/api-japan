<?php

declare(strict_types=1);

namespace App\Story;

use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;
use App\Factory\ApiKeyFactory;

final class ApiKeyStory extends Story
{
    public function build(): void
    {
        ApiKeyFactory::createMany(10, function () {
            return [
                'user' => UserFactory::random(),
                'lastUsedAt' => new \DateTimeImmutable(),
                'expiredAt' => new \DateTimeImmutable('+1year'),
            ];
        });
    }
}
