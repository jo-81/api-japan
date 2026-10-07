<?php

declare(strict_types=1);

namespace App\Tests\Story;

use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class UserLoginStory extends Story
{
    public function build(): void
    {
        UserFactory::createOne([
            'email' => 'admin@example.com',
            'password' => 'X7!kP9@vR2#qL5',
            'roles' => ['ROLE_ADMIN'],
            'emailVerified' => true,
        ]);
    }
}
