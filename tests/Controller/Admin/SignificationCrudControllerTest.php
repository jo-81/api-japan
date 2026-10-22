<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Tests\Utils\CrudTestCase;
use App\Tests\Story\UserLoginStory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Foundry\Attribute\WithStory;
use App\Tests\Story\SignificationTestStory;
use App\Controller\Admin\DashboardController;
use App\Controller\Admin\SignificationCrudController;

#[WithStory(UserLoginStory::class)]
#[WithStory(SignificationTestStory::class)]
class SignificationCrudControllerTest extends CrudTestCase
{
    use ResetDatabase;
    use Factories;

    /**
     * @return class-string<SignificationCrudController> returns the tested Controller Fqcn
     */
    protected function getControllerFqcn(): string
    {
        return SignificationCrudController::class;
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
            ['detail', 'GET', 200],
        ];
    }
}
