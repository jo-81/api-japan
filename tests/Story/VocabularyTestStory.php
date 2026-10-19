<?php

declare(strict_types=1);

namespace App\Tests\Story;

use Zenstruck\Foundry\Story;
use App\Tests\Factory\VocabularyFactory;

final class VocabularyTestStory extends Story
{
    public function build(): void
    {
        VocabularyFactory::createOne([
            'meaning' => 'fleur',
            'kana' => 'はな',
            'romaji' => 'hana',
            'kunrei' => 'hana',
        ]);
    }
}
