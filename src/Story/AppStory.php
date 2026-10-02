<?php

declare(strict_types=1);

namespace App\Story;

use Zenstruck\Foundry\Story;
use Zenstruck\Foundry\Attribute\AsFixture;

#[AsFixture(name: 'main')]
final class AppStory extends Story
{
    public function build(): void
    {
        // SomeFactory::createOne();
    }
}
