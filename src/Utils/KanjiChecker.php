<?php

declare(strict_types=1);

namespace App\Utils;

final class KanjiChecker
{
    private const MAIN_BLOCK = '\x{4E00}-\x{9FFF}';
    private const EXTENSION_A = '\x{3400}-\x{4DBF}';
    private const COMPATIBILITY = '\x{F900}-\x{FAFF}';
    private const ITERATION_MARK = '\x{3005}';

    /**
     * Vérifie que la chaîne ne contient que des kanji (idéogrammes CJK).
     * Les radicaux, la marque de répétition 々 et les kana sont refusés.
     */
    public static function isKanji(string $value, bool $allowIterationMark = false): bool
    {
        $class = self::MAIN_BLOCK.self::EXTENSION_A.self::COMPATIBILITY;
        if ($allowIterationMark) {
            $class .= self::ITERATION_MARK;
        }

        return 1 === preg_match('/\A['.$class.']+\z/u', $value);
    }
}
