<?php

declare(strict_types=1);

namespace App\Story;

use Zenstruck\Foundry\Story;
use App\Factory\ThemeFactory;

final class ThemeStory extends Story
{
    public function build(): void
    {
        ThemeFactory::createMany(20);
    }
}
