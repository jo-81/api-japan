<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Signification;
use Zenstruck\Foundry\Test\Factories;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SignificationTest extends KernelTestCase
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
     * Property meaning
     * -----------------------------------------.
     */
    #[DataProvider('meaningValidationProvider')]
    public function testDataMeaning(string $meaning, int $countError): void
    {
        $signification = new Signification();
        $signification->setMeaning($meaning);

        $violations = $this->validator->validateProperty($signification, 'meaning');

        $this->assertCount($countError, $violations);
    }

    /**
     * @return array<string, array{0: ?string, 1: int}>
     */
    public static function meaningValidationProvider(): array
    {
        return [
            'Ne peut pas être vide' => ['', 1],
            'Ne peut pas être un kana' => ['あ', 1],
            'Ne peut pas être un kanji' => ['日', 1],
            'Ne dépasse pas 255 caractères' => [str_repeat('a', 254).'e', 0],
            'Dépasse 255 caractères' => [str_repeat('a', 255).'e', 1],
            'Donnée valide' => ['jour', 0],
        ];
    }

    /**
     * -----------------------------------------
     * Property onyomi / kunyomi
     * -----------------------------------------.
     */
    #[DataProvider('yomiValidationProvider')]
    public function testDataYomi(?string $data, int $countError): void
    {
        $properties = ['onyomi', 'kunyomi'];

        $signification = new Signification();
        foreach ($properties as $property) {
            $method = 'set'.ucfirst($property);
            $signification->$method($data);
            $violations = $this->validator->validateProperty($signification, $property);

            $this->assertCount($countError, $violations);
        }
    }

    /**
     * @return array<string, array{0: ?string, 1: int}>
     */
    public static function yomiValidationProvider(): array
    {
        return [
            'Ne peut pas être un kana' => ['あ', 1],
            'Ne peut pas être null' => [null, 0],
            'Ne peut pas être un kanji' => ['日', 1],
            'Ne dépasse pas 255 caractères' => [str_repeat('a', 254).'e', 0],
            'Dépasse 255 caractères' => [str_repeat('a', 255).'e', 1],
            'Donnée valide' => ['jour', 0],
        ];
    }
}
