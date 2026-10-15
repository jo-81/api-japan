<?php

declare(strict_types=1);

namespace App\Utils;

final class KanaChecker
{
    private const HIRAGANA = '\x{3041}-\x{3096}';
    private const KATAKANA = '\x{30A1}-\x{30FA}';
    private const PROLONGED_SOUND_MARK = '\x{30FC}';

    /**
     * Vérifie que la chaîne ne contient que des lettres hiragana ou katakana.
     * Les marques d'itération, la ponctuation et les kanji sont refusés.
     */
    public static function isKana(string $value, bool $allowProlongedSoundMark = false): bool
    {
        $class = self::HIRAGANA.self::KATAKANA;
        if ($allowProlongedSoundMark) {
            $class .= self::PROLONGED_SOUND_MARK;
        }

        return 1 === preg_match('/\A['.$class.']+\z/u', $value);
    }
}
