<?php

declare(strict_types=1);

namespace App\Story;

use App\Enum\KanaType;
use App\Enum\KanaAccentType;
use Zenstruck\Foundry\Story;
use App\Tests\Factory\KanaFactory;

class KanaListStory extends Story
{
    public function build(): void
    {
        $kanaData = self::getData();
        $position = 1;

        foreach (['hiragana', 'katakana'] as $typeValue) {
            $type = ('hiragana' === $typeValue) ? KanaType::HIRAGANA : KanaType::KATAKANA;

            foreach ($kanaData as $data) {
                [$romaji, $kunrei, $hiragana, $katakana, $accentType] = $data;
                $ideogram = ('hiragana' === $typeValue) ? $hiragana : $katakana;

                KanaFactory::createOne([
                    'romaji' => $romaji,
                    'kunrei' => $kunrei,
                    'ideogram' => $ideogram,
                    'type' => $type,
                    'accentType' => $accentType,
                    'position' => $position,
                ]);

                ++$position;
            }
        }
    }

    /**
     * @return array<int, array{0: string, 1: string, 2: string, 3: string, 4: KanaAccentType::HANDAKUTEN|KanaAccentType::DAKUTEN|null}>
     */
    private static function getData(): array
    {
        return [
            // [romaji, kunrei, hiragana, katakana, accentType]
            ['a', 'a', 'あ', 'ア', null],
            ['i', 'i', 'い', 'イ', null],
            ['u', 'u', 'う', 'ウ', null],
            ['e', 'e', 'え', 'エ', null],
            ['o', 'o', 'お', 'オ', null],
            ['ka', 'ka', 'か', 'カ', null],
            ['ga', 'ga', 'が', 'ガ', KanaAccentType::DAKUTEN],
            ['ki', 'ki', 'き', 'キ', null],
            ['gi', 'gi', 'ぎ', 'ギ', KanaAccentType::DAKUTEN],
            ['kya', 'kya', 'きゃ', 'キャ', null],
            ['kyu', 'kyu', 'きゅ', 'キュ', null],
            ['kyo', 'kyo', 'きょ', 'キョ', null],
            ['gya', 'gya', 'ぎゃ', 'ギャ', null],
            ['gyu', 'gyu', 'ぎゅ', 'ギュ', null],
            ['gyo', 'gyo', 'ぎょ', 'ギョ', null],
            ['ku', 'ku', 'く', 'ク', null],
            ['gu', 'gu', 'ぐ', 'グ', KanaAccentType::DAKUTEN],
            ['ke', 'ke', 'け', 'ケ', null],
            ['ge', 'ge', 'げ', 'ゲ', KanaAccentType::DAKUTEN],
            ['ko', 'ko', 'こ', 'コ', null],
            ['go', 'go', 'ご', 'ゴ', KanaAccentType::DAKUTEN],
            ['sa', 'sa', 'さ', 'サ', null],
            ['za', 'za', 'ざ', 'ザ', KanaAccentType::DAKUTEN],
            ['shi', 'si', 'し', 'シ', null],
            ['ji', 'zi', 'じ', 'ジ', KanaAccentType::DAKUTEN],
            ['sha', 'sha', 'しゃ', 'シャ', null],
            ['shu', 'shu', 'しゅ', 'シュ', null],
            ['sho', 'sho', 'しょ', 'ショ', null],
            ['ja', 'ja', 'じゃ', 'ジャ', null],
            ['ju', 'ju', 'じゅ', 'ジュ', null],
            ['jo', 'jo', 'じょ', 'ジョ', null],
            ['su', 'su', 'す', 'ス', null],
            ['zu', 'zu', 'ず', 'ズ', KanaAccentType::DAKUTEN],
            ['se', 'se', 'せ', 'セ', null],
            ['ze', 'ze', 'ぜ', 'ゼ', KanaAccentType::DAKUTEN],
            ['so', 'so', 'そ', 'ソ', null],
            ['zo', 'zo', 'ぞ', 'ゾ', KanaAccentType::DAKUTEN],
            ['ta', 'ta', 'た', 'タ', null],
            ['da', 'da', 'だ', 'ダ', KanaAccentType::DAKUTEN],
            ['chi', 'ti', 'ち', 'チ', null],
            ['ji', 'ji', 'ぢ', 'ヂ', KanaAccentType::DAKUTEN],
            ['cha', 'cha', 'ちゃ', 'チャ', null],
            ['chu', 'chu', 'ちゅ', 'チュ', null],
            ['cho', 'cho', 'ちょ', 'チョ', null],
            ['tsu', 'tsu', 'つ', 'ツ', null],
            ['zu', 'zu', 'づ', 'ヅ', KanaAccentType::DAKUTEN],
            ['te', 'te', 'て', 'テ', null],
            ['de', 'de', 'で', 'デ', KanaAccentType::DAKUTEN],
            ['to', 'to', 'と', 'ト', null],
            ['do', 'do', 'ど', 'ド', KanaAccentType::DAKUTEN],
            ['na', 'na', 'な', 'ナ', null],
            ['ni', 'ni', 'に', 'ニ', null],
            ['nya', 'nya', 'にゃ', 'ニャ', null],
            ['nyu', 'nyu', 'にゅ', 'ニュ', null],
            ['nyo', 'nyo', 'にょ', 'ニョ', null],
            ['nu', 'nu', 'ぬ', 'ヌ', null],
            ['ne', 'ne', 'ね', 'ネ', null],
            ['no', 'no', 'の', 'ノ', null],
            ['ha', 'ha', 'は', 'ハ', null],
            ['ba', 'ba', 'ば', 'バ', KanaAccentType::DAKUTEN],
            ['pa', 'pa', 'ぱ', 'パ', KanaAccentType::HANDAKUTEN],
            ['hi', 'hi', 'ひ', 'ヒ', null],
            ['bi', 'bi', 'び', 'ビ', KanaAccentType::DAKUTEN],
            ['pi', 'pi', 'ぴ', 'ピ', KanaAccentType::HANDAKUTEN],
            ['hya', 'hya', 'ひゃ', 'ヒャ', null],
            ['hyu', 'hyu', 'ひゅ', 'ヒュ', null],
            ['hyo', 'hyo', 'ひょ', 'ヒョ', null],
            ['bya', 'bya', 'びゃ', 'ビャ', null],
            ['byu', 'byu', 'びゅ', 'ビュ', null],
            ['byo', 'byo', 'びょ', 'ビョ', null],
            ['pya', 'pya', 'ぴゃ', 'ピャ', null],
            ['pyu', 'pyu', 'ぴゅ', 'ピュ', null],
            ['pyo', 'pyo', 'ぴょ', 'ピョ', null],
            ['fu', 'fu', 'ふ', 'フ', null],
            ['bu', 'bu', 'ぶ', 'ブ', KanaAccentType::DAKUTEN],
            ['pu', 'pu', 'ぷ', 'プ', KanaAccentType::HANDAKUTEN],
            ['he', 'he', 'へ', 'ヘ', null],
            ['be', 'be', 'べ', 'ベ', KanaAccentType::DAKUTEN],
            ['pe', 'pe', 'ぺ', 'ペ', KanaAccentType::HANDAKUTEN],
            ['ho', 'ho', 'ほ', 'ホ', null],
            ['bo', 'bo', 'ぼ', 'ボ', KanaAccentType::DAKUTEN],
            ['po', 'po', 'ぽ', 'ポ', KanaAccentType::HANDAKUTEN],
            ['ma', 'ma', 'ま', 'マ', null],
            ['mi', 'mi', 'み', 'ミ', null],
            ['mya', 'mya', 'みゃ', 'ミャ', null],
            ['myu', 'myu', 'みゅ', 'ミュ', null],
            ['myo', 'myo', 'みょ', 'ミョ', null],
            ['mu', 'mu', 'む', 'ム', null],
            ['me', 'me', 'め', 'メ', null],
            ['mo', 'mo', 'も', 'モ', null],
            ['ya', 'ya', 'や', 'ヤ', null],
            ['yu', 'yu', 'ゆ', 'ユ', null],
            ['yo', 'yo', 'よ', 'ヨ', null],
            ['ra', 'ra', 'ら', 'ラ', null],
            ['ri', 'ri', 'り', 'リ', null],
            ['rya', 'rya', 'りゃ', 'リャ', null],
            ['ryu', 'ryu', 'りゅ', 'リュ', null],
            ['ryo', 'ryo', 'りょ', 'リョ', null],
            ['ru', 'ru', 'る', 'ル', null],
            ['re', 're', 'れ', 'レ', null],
            ['ro', 'ro', 'ろ', 'ロ', null],
            ['wa', 'wa', 'わ', 'ワ', null],
            ['wo', 'wo', 'を', 'ヲ', null],
            ['n', 'n', 'ん', 'ン', null],
        ];
    }
}
