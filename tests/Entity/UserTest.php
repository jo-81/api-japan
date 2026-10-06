<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use PHPUnit\Framework\Attributes\DataProvider;

class UserTest extends TestCase
{
    #[DataProvider('emailValidationProvider')]
    public function testEmailValidation(string $email, int $countError): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator()
        ;

        $user = new User();
        $user->setEmail($email);

        $violations = $validator->validate($user);

        $this->assertCount($countError, $violations);
    }

    /**
     * @return array<int, array{0: string, 1: int}>
     */
    public static function emailValidationProvider(): array
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

    public function testPrePersist(): void
    {
        $user = new User();

        $user->onPrePersist();

        $this->assertInstanceOf(\DateTimeImmutable::class, $user->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $user->getUpdatedAt());
        $this->assertSame(
            $user->getCreatedAt(),
            $user->getUpdatedAt(),
        );
    }

    public function testPreUpdate(): void
    {
        $user = new User();

        $user->onPrePersist();

        $createdAt = $user->getCreatedAt();
        $updatedAt = $user->getUpdatedAt();

        usleep(1000);

        $user->onPreUpdate();

        $this->assertSame(
            $createdAt,
            $user->getCreatedAt(),
        );

        $this->assertNotSame(
            $updatedAt,
            $user->getUpdatedAt(),
        );
    }
}
