<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Story\UserStory;
use App\Tests\Story\ThemeTestStory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
class TestFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserStory::load();
        ThemeTestStory::load();
    }
}
