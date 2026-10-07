<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\User;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserDoctrineTest extends KernelTestCase
{
    public function testUserGetsUuidV7(): void
    {
        self::bootKernel();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);

        $user = new User();
        $user->setEmail('uuid-test@example.com');
        $user->setPassword('dummy-hash');

        $entityManager->persist($user);
        $entityManager->flush();

        $id = $user->getId();

        self::assertInstanceOf(Uuid::class, $id);
        self::assertInstanceOf(UuidV7::class, $id);
    }
}
