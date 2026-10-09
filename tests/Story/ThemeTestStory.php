<?php

declare(strict_types=1);

namespace App\Tests\Story;

use Zenstruck\Foundry\Story;
use App\Factory\ThemeFactory;

final class ThemeTestStory extends Story
{
    public function build(): void
    {
        ThemeFactory::createOne(['name' => 'un theme spécifique']);
        ThemeFactory::createMany(19);
    }
}
