<?php

declare(strict_types=1);

namespace App\Tests\Story;

use Zenstruck\Foundry\Story;
use App\Tests\Factory\KanjiFactory;

class KanjiTestStory extends Story
{
    public function build(): void
    {
        KanjiFactory::createOne([]);
    }
}
