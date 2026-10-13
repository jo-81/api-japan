<?php

declare(strict_types=1);

namespace App\Tests\Story;

use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;
use App\Factory\ApiKeyFactory;

final class UserLoginStory extends Story
{
    public function build(): void
    {
        $admin = UserFactory::createOne([
            'email' => 'admin@example.com',
            'password' => 'X7!kP9@vR2#qL5',
            'roles' => ['ROLE_ADMIN'],
            'emailVerified' => true,
        ]);

        $user = UserFactory::createOne([
            'email' => 'user@example.com',
            'password' => 'X7!kP9@vR2#qL5',
            'emailVerified' => true,
        ]);

        UserFactory::createOne([
            'email' => 'email-not-verified@example.com',
            'password' => 'X7!kP9@vR2#qL5',
            'emailVerified' => false,
        ]);

        // Création de 3 clés API pour chaque utilisateur
        foreach ([$admin, $user] as $u) {
            for ($i = 1; $i <= 3; ++$i) {
                ApiKeyFactory::createOne([
                    'user' => $u,
                    'name' => "Clé API $i",
                    'status' => \App\Enum\ApiKeyStatus::ACTIVE,
                ]);
            }
        }
    }
}
