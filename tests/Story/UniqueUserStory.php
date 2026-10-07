<?php

declare(strict_types=1);

namespace App\Tests\Story;

use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class UniqueUserStory extends Story
{
    public function build(): void
    {
        UserFactory::createOne(['email' => 'test-not-unique@example.com']);
    }
}
