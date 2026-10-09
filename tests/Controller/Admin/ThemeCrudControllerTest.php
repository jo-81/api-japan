<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Tests\Utils\CrudTestCase;
use App\Tests\Story\ThemeTestStory;
use App\Tests\Story\UserLoginStory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Foundry\Attribute\WithStory;
use App\Controller\Admin\DashboardController;
use App\Controller\Admin\ThemeCrudController;

#[WithStory(UserLoginStory::class)]
#[WithStory(ThemeTestStory::class)]
class ThemeCrudControllerTest extends CrudTestCase
{
    use ResetDatabase;
    use Factories;

    /**
     * @return class-string<ThemeCrudController> returns the tested Controller Fqcn
     */
    protected function getControllerFqcn(): string
    {
        return ThemeCrudController::class;
    }

    /**
     * @return class-string<DashboardController> returns the tested Controller Fqcn
     */
    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }

    /**
     * @return array<int, array{0: string, 1: string, 2: int}>
     */
    public static function getDataProviderWhenUserLoggedWithRoleAdmin(): array
    {
        return [
            ['index', 'GET', 200],
            ['detail', 'GET', 200],
        ];
    }
}
