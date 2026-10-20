<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Kanji;
use App\Tests\Story\KanjiTestStory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Attribute\WithStory;
use PHPUnit\Framework\Attributes\DataProvider;
use Zenstruck\Foundry\Attribute\ResetDatabase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[WithStory(KanjiTestStory::class)]
#[ResetDatabase]
class KanjiTest extends KernelTestCase
{
    use Factories;

    private ValidatorInterface $validator;

    #[\Override]
    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->validator = $container->get(ValidatorInterface::class);

        parent::setUp();
    }

    /**
     * -----------------------------------------
     * Property position
     * -----------------------------------------.
     */
    #[DataProvider('positionValidationProvider')]
    public function testInvalidPosition(int $position): void
    {
        $kanji = new Kanji();
        $kanji->setPosition($position);

        $violations = $this->validator->validateProperty($kanji, 'position');

        $this->assertCount(1, $violations);
    }

    /**
     * @return array<int, array{0: int}>
     */
    public static function positionValidationProvider(): array
    {
        return [
            [-1], [0],
        ];
    }

    public function testUniquePosition(): void
    {
        $kanji = $this->getKanji();
        $kanji->setPosition(2);

        $violations = $this->validator->validate($kanji);

        $this->assertCount(1, $violations);
    }

    /**
     * -----------------------------------------
     * Property numberStroke
     * -----------------------------------------.
     */
    #[DataProvider('numberStrokeValidationProvider')]
    public function testInvalidNumberStoke(int $numberStroke): void
    {
        $kanji = new Kanji();
        $kanji->setNumberStroke($numberStroke);

        $violations = $this->validator->validateProperty($kanji, 'numberStroke');

        $this->assertCount(1, $violations);
    }

    /**
     * @return array<int, array{0: int}>
     */
    public static function numberStrokeValidationProvider(): array
    {
        return [
            [-1], [0],
        ];
    }

    /**
     * -----------------------------------------
     * Property ideogram
     * -----------------------------------------.
     */
    #[DataProvider('ideogramValidationProvider')]
    public function testInvalidIdeogram(string $ideogram): void
    {
        $kanji = new Kanji();
        $kanji->setIdeogram($ideogram);

        $violations = $this->validator->validateProperty($kanji, 'ideogram');

        $this->assertCount(1, $violations);
    }

    /**
     * @return array<int, array{0: string}>
     */
    public static function ideogramValidationProvider(): array
    {
        return [
            [''], ['a'], ['あ'], ['明日'],
        ];
    }

    public function testUniqueIdeogram(): void
    {
        $kanji = $this->getKanji();
        $kanji->setIdeogram('一');

        $violations = $this->validator->validate($kanji);

        $this->assertCount(1, $violations);
    }

    /**
     * -----------------------------------------
     * Property meanings
     * -----------------------------------------.
     */

    /**
     * @param string[] $meanings
     */
    #[DataProvider('meaningsValidationProvider')]
    public function testInvalidMeanings(array $meanings): void
    {
        $kanji = new Kanji();
        $kanji->setMeanings($meanings);

        $violations = $this->validator->validateProperty($kanji, 'meanings');

        $this->assertCount(1, $violations);
    }

    /**
     * @return iterable<array<int, array<int, int|string>>>
     */
    public static function meaningsValidationProvider(): iterable
    {
        yield [['']];
        yield [['人']];
        yield [['あ']];
    }

    /**
     * -----------------------------------------
     * Property onyomi
     * -----------------------------------------.
     */

    /**
     * @param string[] $onyomi
     */
    #[DataProvider('readingValidationProvider')]
    public function testInvalidOnyomi(array $onyomi): void
    {
        $kanji = new Kanji();
        $kanji->setOnyomi($onyomi);

        $violations = $this->validator->validateProperty($kanji, 'onyomi');

        $this->assertCount(1, $violations);
    }

    /**
     * -----------------------------------------
     * Property kunyomi
     * -----------------------------------------.
     */

    /**
     * @param string[] $kunyomi
     */
    #[DataProvider('readingValidationProvider')]
    public function testInvalidKunyomi(array $kunyomi): void
    {
        $kanji = new Kanji();
        $kanji->setKunyomi($kunyomi);

        $violations = $this->validator->validateProperty($kanji, 'kunyomi');

        $this->assertCount(1, $violations);
    }

    /**
     * @return iterable<array<int, array<int, int|string>>>
     */
    public static function readingValidationProvider(): iterable
    {
        yield [['人']];
        yield [['あ']];
    }

    public function testValidKanji(): void
    {
        $violations = $this->validator->validate($this->getKanji());

        $this->assertCount(0, $violations);
    }

    private function getKanji(): Kanji
    {
        return new Kanji()
            ->setPosition(1)
            ->setNumberStroke(2)
            ->setIdeogram('人')
            ->setMeanings(['personne', 'homme', 'être humain'])
            ->setKunyomi(['hito'])
            ->setOnyomi(['NIN', 'JIN'])
        ;
    }
}
