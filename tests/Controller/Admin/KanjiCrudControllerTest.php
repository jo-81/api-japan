<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Tests\Utils\CrudTestCase;
use App\Tests\Story\UserLoginStory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Foundry\Attribute\WithStory;
use App\Controller\Admin\DashboardController;
use App\Controller\Admin\KanjiCrudController;

#[WithStory(UserLoginStory::class)]
class KanjiCrudControllerTest extends CrudTestCase
{
    use ResetDatabase;
    use Factories;

    /**
     * @return class-string<KanjiCrudController> returns the tested Controller Fqcn
     */
    protected function getControllerFqcn(): string
    {
        return KanjiCrudController::class;
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
            ['new', 'GET', 200],
            ['new', 'POST', 200],
        ];
    }
}
