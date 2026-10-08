<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Entity\User;
use App\Tests\Utils\CrudTestCase;
use App\Tests\Story\UserLoginStory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Foundry\Attribute\WithStory;
use App\Controller\Admin\UserCrudController;
use App\Controller\Admin\DashboardController;
use PHPUnit\Framework\Attributes\DataProvider;

#[WithStory(UserLoginStory::class)]
class UserCrudControllerTest extends CrudTestCase
{
    use ResetDatabase;
    use Factories;

    /**
     * @return class-string<UserCrudController> returns the tested Controller Fqcn
     */
    protected function getControllerFqcn(): string
    {
        return UserCrudController::class;
    }

    /**
     * @return class-string<DashboardController> returns the tested Controller Fqcn
     */
    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }

    #[DataProvider('getDataProviderWhenUserLogged')]
    public function testAccessPageWhenUserLogged(
        string $action,
        string $httpMethod,
        int $statusCode,
        ?string $email = null,
    ): void {
        $entityId = 0;

        if (!is_null($email)) {
            /** @var ?User $user */
            $user = $this->getUserLogged(['email' => $email]);

            if (!is_null($user)) {
                $entityId = $user->getId();
                $this->client->loginUser($user);
            }
        }

        $this->client->request($httpMethod, $this->getAdminLink($action, $entityId)); // @phpstan-ignore-line

        static::assertResponseStatusCodeSame($statusCode);
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: int, 3?: string|null}>
     */
    public static function getDataProviderWhenUserLogged(): array
    {
        return [
            'Utilisateur avec un rôle ROLE_ADMIN' => ['index', 'GET', 200, 'admin@example.com'],
            'Utilisateur avec un rôle ROLE_USER' => ['index', 'GET', 403, 'user@example.com'],
            'Utilisateur non connecté' => ['index', 'GET', 302],
        ];
    }
}
