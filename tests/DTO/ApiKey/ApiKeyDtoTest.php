<?php

declare(strict_types=1);

namespace App\Tests\DTO\ApiKey;

use App\DTO\ApiKey\ApiKeyDto;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use PHPUnit\Framework\Attributes\DataProvider;

class ApiKeyDtoTest extends TestCase
{
    #[DataProvider('getDataInvalidName')]
    public function testNameInvalid(?string $name): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $dto = new ApiKeyDto();
        $dto->name = $name;

        $violations = $validator->validateProperty($dto, 'name');

        $this->assertCount(1, $violations);
    }

    /** @return array<int, array{0: ?string}> */
    public static function getDataInvalidName(): array
    {
        return [
            [''],
            [null],
            [str_repeat('a', 249).'example'],
        ];
    }
}
