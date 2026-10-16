<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Tests\Utils\CrudTestCase;
use App\Tests\Story\KanaTestStory;
use App\Tests\Story\UserLoginStory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Foundry\Attribute\WithStory;
use App\Controller\Admin\KanaCrudController;
use App\Controller\Admin\DashboardController;

#[WithStory(UserLoginStory::class)]
#[WithStory(KanaTestStory::class)]
class KanaCrudControllerTest extends CrudTestCase
{
    use ResetDatabase;
    use Factories;

    /**
     * @return class-string<KanaCrudController> returns the tested Controller Fqcn
     */
    protected function getControllerFqcn(): string
    {
        return KanaCrudController::class;
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
            ['new', 'GET', 403],
            ['edit', 'GET', 200],
            ['detail', 'GET', 200],
            ['delete', 'POST', 403],
        ];
    }
}
