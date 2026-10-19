<?php

declare(strict_types=1);

namespace App\Story;

use App\Enum\JlptLevel;
use Zenstruck\Foundry\Story;
use App\Factory\ThemeFactory;
use App\Factory\VocabularyFactory;

use function Zenstruck\Foundry\faker;

final class VocabularyStory extends Story
{
    public function build(): void
    {
        VocabularyFactory::createMany(50, function ($i) {
            return [
                'jlpt' => (0 === $i % 3)
                    ? faker()->randomElement(JlptLevel::cases())
                    : null,
                'theme' => (0 === $i % 2)
                    ? ThemeFactory::random()
                    : null,
            ];
        });
    }
}
