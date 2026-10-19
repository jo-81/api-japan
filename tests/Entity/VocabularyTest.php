<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Vocabulary;
use Zenstruck\Foundry\Test\Factories;
use App\Tests\Story\VocabularyTestStory;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Foundry\Attribute\WithStory;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[WithStory(VocabularyTestStory::class)]
class Vocabularytest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

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
     * Property meaning / romaji / kunrei
     * -----------------------------------------.
     */
    #[DataProvider('stringValidationProvider')]
    public function testInvalidString(string $value, int $countError): void
    {
        $properties = ['meaning', 'romaji', 'kunrei'];

        $vocabulary = new Vocabulary();
        foreach ($properties as $property) {
            $method = 'set'.ucfirst($property);
            $vocabulary->$method($value);

            $violations = $this->validator->validateProperty($vocabulary, $property);

            $this->assertCount($countError, $violations);
        }
    }

    public function testUniqueMeaning(): void
    {
        $vocabulary = $this->getVocabulary();
        $vocabulary->setMeaning('fleur');

        $violations = $this->validator->validate($vocabulary);

        $this->assertCount(1, $violations);
    }

    /**
     * @return array<int, array{0: string, 1: int}>
     */
    public static function stringValidationProvider(): array
    {
        return [
            ['', 1],
            [str_repeat('a', 254).'e', 0],
        ];
    }

    /**
     * -----------------------------------------
     * Property kana
     * -----------------------------------------.
     */
    #[DataProvider('kanaValidationProvider')]
    public function testInvalidKana(string $value): void
    {
        $vocabulary = new Vocabulary();
        $vocabulary->setKana($value);

        $violations = $this->validator->validateProperty($vocabulary, 'kana');

        $this->assertCount(1, $violations);
    }

    /**
     * @return array<int, array{0: string}>
     */
    public static function kanaValidationProvider(): array
    {
        return [
            [''],
            [str_repeat('あ', 256)],
            ['abc'],
        ];
    }

    public function testValidVocabulary(): void
    {
        $violations = $this->validator->validate($this->getVocabulary());

        $this->assertCount(0, $violations);
    }

    private function getVocabulary(): Vocabulary
    {
        return new Vocabulary()
            ->setMeaning('mot de vocabulaire')
            ->setKana('あ')
            ->setRomaji('a')
            ->setKunrei('a')
        ;
    }
}
