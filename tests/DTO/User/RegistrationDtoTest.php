<?php

declare(strict_types=1);

namespace App\Tests\DTO\User;

use PHPUnit\Framework\TestCase;
use App\DTO\User\RegistrationDto;
use Symfony\Component\Validator\Validation;
use PHPUnit\Framework\Attributes\DataProvider;

class RegistrationDtoTest extends TestCase
{
    #[DataProvider('getDataProviderEmail')]
    public function testEmailValid(string $email, int $countError): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $dto = new RegistrationDto();
        $dto->email = $email;
        $dto->plainPassword = 'X7!kP9@vR2#qL5';
        $dto->passwordConfirmation = 'X7!kP9@vR2#qL5';

        $violations = $validator->validateProperty($dto, 'email');

        $this->assertCount($countError, $violations);
    }

    /**
     * @return array<int, array{0: string, 1: int}>
     */
    public static function getDataProviderEmail(): array
    {
        return [
            ['test@example.com', 0],
            ['', 1],
            ['bonjour', 1],
            ['test@', 1],
            [str_repeat('a', 168).'@example.com', 0],
            [str_repeat('a', 169).'@example.com', 1],
        ];
    }

    #[DataProvider('getDataProviderPlainPassword')]
    public function testPlainPasswordValid(
        string $password,
        int $countError,
    ): void {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $dto = new RegistrationDto();
        $dto->plainPassword = $password;

        $violations = $validator->validateProperty(
            $dto,
            'plainPassword',
        );

        $this->assertCount($countError, $violations);
    }

    /**
     * @return array<int, array{0: string, 1: int}>
     */
    public static function getDataProviderPlainPassword(): array
    {
        return [
            ['', 3],
            ['1234567', 2],
            ['password', 1],
            ['Password123!', 1],
            ['X7!kP9@vR2#qL5', 0],
        ];
    }

    #[DataProvider('getDataProviderPasswordConfirmation')]
    public function testPasswordConfirmationValid(
        string $password,
        string $confirmation,
        int $countError,
    ): void {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $dto = new RegistrationDto();
        $dto->plainPassword = $password;
        $dto->passwordConfirmation = $confirmation;

        $violations = $validator->validateProperty(
            $dto,
            'passwordConfirmation',
        );

        $this->assertCount($countError, $violations);
    }

    /**
     * @return array<int, array{0: string, 1: string, 2: int}>
     */
    public static function getDataProviderPasswordConfirmation(): array
    {
        return [
            [
                'X7!kP9@vR2#qL5',
                'X7!kP9@vR2#qL5',
                0,
            ],
            [
                'X7!kP9@vR2#qL5',
                'X7!kP9@vR2#qL6',
                1,
            ],
            [
                'X7!kP9@vR2#qL5',
                '',
                2,
            ],
        ];
    }
}
