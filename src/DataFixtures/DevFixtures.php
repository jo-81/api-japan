<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Story\UserStory;
use App\Story\ThemeStory;
use App\Tests\Story\UserLoginStory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'dev')]
class DevFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserStory::load();
        UserLoginStory::load();
        ThemeStory::load();
    }
}
