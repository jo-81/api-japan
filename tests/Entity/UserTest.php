<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\User;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    private ValidatorInterface $validator;

    #[\Override]
    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->validator = $container->get('validator');

        parent::setUp();
    }

    #[DataProvider('emailValidationProvider')]
    public function testEmailValidation(string $email, int $countError): void
    {
        $user = new User();
        $user->setEmail($email);

        $violations = $this->validator->validate($user);

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

    public function testUniqueEntity(): void
    {
        UserFactory::createOne([
            'email' => 'test-not-unique@example.com',
        ]);

        $user = new User();
        $user->setEmail('test-not-unique@example.com');

        $violations = $this->validator->validate($user);

        $this->assertCount(1, $violations);
        $this->assertSame('email', $violations[0]?->getPropertyPath());
        $this->assertSame(
            'Cette adresse email est déjà utilisée.',
            $violations[0]->getMessage(),
        );
    }
}
