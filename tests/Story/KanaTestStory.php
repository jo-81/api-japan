<?php

declare(strict_types=1);

namespace App\Tests\Story;

use App\Enum\KanaType;
use Zenstruck\Foundry\Story;
use App\Tests\Factory\KanaFactory;

final class KanaTestStory extends Story
{
    public function build(): void
    {
        KanaFactory::createOne([
            'romaji' => 'a',
            'ideogram' => 'あ',
            'kunrei' => 'a',
            'type' => KanaType::HIRAGANA,
            'accentType' => null,
            'position' => 1,
        ]);

        KanaFactory::createOne([
            'romaji' => 'a',
            'ideogram' => 'ア',
            'kunrei' => 'a',
            'type' => KanaType::KATAKANA,
            'accentType' => null,
            'position' => 20,
        ]);
    }
}
