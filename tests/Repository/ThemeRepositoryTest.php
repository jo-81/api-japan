<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\Theme;
use App\Tests\Story\ThemeTestStory;
use Zenstruck\Foundry\Test\Factories;
use Doctrine\ORM\EntityManagerInterface;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Foundry\Attribute\WithStory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

#[WithStory(ThemeTestStory::class)]
class ThemeRepositoryTest extends KernelTestCase
{
    use ResetDatabase;
    use Factories;

    public function testNumberThmeInRepository(): void
    {
        self::bootKernel();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);

        $themeRepository = $entityManager->getRepository(Theme::class);

        $this->assertSame(20, $themeRepository->count());
    }
}
