<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Kana;
use App\Enum\KanaType;
use App\Tests\Story\KanaTestStory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Attribute\WithStory;
use PHPUnit\Framework\Attributes\DataProvider;
use Zenstruck\Foundry\Attribute\ResetDatabase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[WithStory(KanaTestStory::class)]
#[ResetDatabase]
class KanaTest extends KernelTestCase
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
     * Property romaji
     * -----------------------------------------.
     */
    #[DataProvider('romajiValidationProvider')]
    public function testInvalidRomaji(string $romaji, int $countError): void
    {
        $kana = new Kana();
        $kana->setRomaji($romaji);

        $violations = $this->validator->validateProperty($kana, 'romaji');

        $this->assertCount($countError, $violations);
    }

    /**
     * @return array<int, array{0: string, 1: int}>
     */
    public static function romajiValidationProvider(): array
    {
        return [
            ['', 1],
            [str_repeat('a', 9).'e', 0],
            [str_repeat('a', 10).'e', 1],
        ];
    }

    /**
     * -----------------------------------------
     * Property ideogram
     * -----------------------------------------.
     */
    #[DataProvider('ideogramValidationProvider')]
    public function testInvalidIdeogram(string $ideogram, int $countError): void
    {
        $kana = new Kana();
        $kana->setIdeogram($ideogram);

        $violations = $this->validator->validateProperty($kana, 'ideogram');

        $this->assertCount($countError, $violations);
    }

    /**
     * @return array<int, array{0: string, 1: int}>
     */
    public static function ideogramValidationProvider(): array
    {
        return [
            ['a', 1],
            ['', 1],
            ['あ', 0],
        ];
    }

    public function testUniqueIdeogram(): void
    {
        $kana = $this->getKana();
        $kana->setIdeogram('あ');

        $violations = $this->validator->validate($kana);

        $this->assertCount(1, $violations);
    }

    /**
     * -----------------------------------------
     * Property kunrei
     * -----------------------------------------.
     */
    #[DataProvider('kunreiValidationProvider')]
    public function testInvalidKunrei(string $kunrei, int $countError): void
    {
        $kana = new Kana();
        $kana->setKunrei($kunrei);

        $violations = $this->validator->validateProperty($kana, 'kunrei');

        $this->assertCount($countError, $violations);
    }

    /**
     * @return array<int, array{0: string, 1: int}>
     */
    public static function kunreiValidationProvider(): array
    {
        return [
            ['', 1],
            [str_repeat('a', 9).'e', 0],
            [str_repeat('a', 10).'e', 1],
        ];
    }

    /**
     * -----------------------------------------
     * Property position
     * -----------------------------------------.
     */
    #[DataProvider('positionValidationProvider')]
    public function testInvalidPosition(int $position, int $countError): void
    {
        $kana = new Kana();
        $kana->setPosition($position);

        $violations = $this->validator->validateProperty($kana, 'position');

        $this->assertCount($countError, $violations);
    }

    /**
     * @return array<int, array{0: int, 1: int}>
     */
    public static function positionValidationProvider(): array
    {
        return [
            [0, 1],
            [-1, 1],
        ];
    }

    public function testUniquePositionType(): void
    {
        $kana = $this->getKana();
        $kana->setPosition(1);

        $violations = $this->validator->validate($kana);

        $this->assertCount(1, $violations);
    }

    /**
     * -----------------------------------------
     * Property type
     * -----------------------------------------.
     */
    public function testInvalidType(): void
    {
        $kana = new Kana();

        $violations = $this->validator->validateProperty($kana, 'type');

        $this->assertCount(1, $violations);
    }

    /**
     * -----------------------------------------
     * Property File
     * -----------------------------------------.
     */
    #[DataProvider('imageFileValidationProvider')]
    public function testInvalidFile(string $file, string $mimeType, int $countError): void
    {
        $kana = new Kana();
        $filePath = sprintf(self::getContainer()->getParameter('kernel.project_dir').'/%s', $file);
        $file = new UploadedFile($filePath, 'test.file', $mimeType, null, true);

        $kana->setImageFile($file);

        $violations = $this->validator->validateProperty($kana, 'imageFile');

        $this->assertCount($countError, $violations);
    }

    /**
     * @return array<int, array{0: string, 1: string, 2: int}>
     */
    public static function imageFileValidationProvider(): array
    {
        return [
            ['/tests/fixtures/files/kana/invalid.jpeg', 'image/jpeg', 1],
            ['/tests/fixtures/files/kana/too_big.svg', 'image/svg+xml', 1],
        ];
    }

    /**
     * -----------------------------------------
     * Property components
     * -----------------------------------------.
     */

    /**
     * @param array<int, Kana> $data
     */
    #[DataProvider('componentsValidationProvider')]
    public function testInvalidComponents(array $data, int $countError): void
    {
        $kana = new Kana();
        foreach ($data as $k) {
            $kana->addComponent($k);
        }

        $violations = $this->validator->validateProperty($kana, 'components');

        $this->assertCount($countError, $violations);
    }

    /**
     * @return array<int, array{0: array<int, Kana>, 1: int}>
     */
    public static function componentsValidationProvider(): array
    {
        return [
            [[new Kana(), new Kana(), new Kana()], 1],
            [[new Kana()], 1],
            [[], 0],
        ];
    }

    public function testValidKana(): void
    {
        $violations = $this->validator->validate($this->getKana());

        $this->assertCount(0, $violations);
    }

    private function getKana(): Kana
    {
        $filePath = self::getContainer()->getParameter('kernel.project_dir').'/tests/fixtures/files/kana/valid.svg';
        $file = new UploadedFile($filePath, 'test.file', 'image/svg+xml', null, true);

        $kana = new Kana()
            ->setRomaji('i')
            ->setType(KanaType::HIRAGANA)
            ->setIdeogram('い')
            ->setPosition(2)
            ->setKunrei('i')
            ->addComponent(new Kana())
            ->addComponent(new Kana())
        ;

        $kana->setImageFile($file);

        return $kana;
    }
}
