<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Story\UserStory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserStory::load();
    }
}
