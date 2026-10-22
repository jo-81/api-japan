<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Kanji;
use App\Entity\Signification;
use Zenstruck\Foundry\Test\Factories;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * Property position / numberStroke
     * -----------------------------------------.
     */

    /**
     * Pour vérifier les propriétés $position et $numberStroke.
     */
    #[DataProvider('integerValidationProvider')]
    public function testDataInterger(int $data, int $countError): void
    {
        $properties = ['position', 'numberStroke'];

        $kanji = new Kanji();
        foreach ($properties as $property) {
            $method = 'set'.ucfirst($property);
            $kanji->$method($data);
            $violations = $this->validator->validateProperty($kanji, $property);

            $this->assertCount($countError, $violations);
        }
    }

    /**
     * @return array<string, array{0: int, 1: int}>
     */
    public static function integerValidationProvider(): array
    {
        return [
            'Ne peut pas valoir 0' => [0, 1],
            'Doit être strictement positive' => [-1, 1],
            'Donnée valide' => [1, 0],
        ];
    }

    /**
     * -----------------------------------------
     * Property ideogram
     * -----------------------------------------.
     */
    #[DataProvider('ideogramValidationProvider')]
    public function testDataIdeogram(string $data, int $countError): void
    {
        $kanji = new Kanji();
        $kanji->setIdeogram($data);
        $violations = $this->validator->validateProperty($kanji, 'ideogram');

        $this->assertCount($countError, $violations);
    }

    /**
     * @return array<string, array{0: string, 1: int}>
     */
    public static function ideogramValidationProvider(): array
    {
        return [
            'Ne peut pas être une chaine de caractère' => ['j', 1],
            'Ne peut pas être un kana' => ['あ', 1],
            'Doit contenir 1 seul kanji' => ['日日', 1],
            'Ne peut pas être vide' => ['', 1],
            'Donnée valide' => ['日', 0],
        ];
    }

    /**
     * -----------------------------------------
     * Property significations
     * -----------------------------------------.
     */
    public function testDataSignifications(): void
    {
        $violations = $this->validator->validateProperty(new Kanji(), 'significations');

        $this->assertCount(1, $violations);
    }

    public function testInvalidSignification(): void
    {
        $signification = new Signification();
        $signification->setMeaning('');

        $kanji = new Kanji()->addSignification($signification);

        $violations = $this->validator->validateProperty($kanji, 'significations');

        $this->assertCount(1, $violations);
    }
}
