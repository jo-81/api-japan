<?php

declare(strict_types=1);

namespace App\Tests\Story;

use Zenstruck\Foundry\Story;
use App\Tests\Factory\SignificationFactory;

final class SignificationTestStory extends Story
{
    public function build(): void
    {
        SignificationFactory::createOne([]);
    }
}
