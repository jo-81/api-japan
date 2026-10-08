<?php

declare(strict_types=1);

namespace App\Tests\Utils;

use App\Entity\User;
use Symfony\Component\Uid\UuidV7;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Security\Core\User\UserInterface;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Controller\CrudControllerInterface;

/**
 * @extends AbstractCrudTestCase<CrudControllerInterface>
 */
abstract class CrudTestCase extends AbstractCrudTestCase
{
    /**
     * @param array<string, mixed> $criteria
     */
    protected function getUserLogged(array $criteria): ?UserInterface
    {
        $userRepository = $this->entityManager->getRepository(User::class);

        return $userRepository->findOneBy($criteria);
    }

    protected function getAdminLink(string $action, int|string|UuidV7 $entityId = 0): string
    {
        if ($entityId instanceof UuidV7) {
            $entityId = $entityId->toString();
        }

        return match ($action) {
            'index' => $this->generateIndexUrl(),
            'new' => $this->generateNewFormUrl(),
            'detail' => $this->generateDetailUrl($entityId),
            'edit' => $this->generateEditFormUrl($entityId),
            'delete' => $this->getCrudUrl(
                'delete',
                $entityId,
                [],
                $this->getDashboardFqcn(),
                $this->getControllerFqcn(),
            ),
            default => throw new \InvalidArgumentException('Action non gérée'),
        };
    }

    #[DataProvider('getDataProviderWhenUserLoggedWithRoleAdmin')]
    public function testAccessPageWhenUserLogged(string $action, string $httpMethod, int $statusCode): void
    {
        $this->accessPage($httpMethod, $action, $statusCode, 'admin@example.com');
    }

    #[DataProvider('getDataProviderWhenUserLoggedWithRoleNotAdmin')]
    public function testAccessPageWhenUserLoggedWithRoleNotAdmin(string $action, string $httpMethod, int $statusCode): void
    {
        $this->accessPage($httpMethod, $action, $statusCode, 'user@example.com');
    }

    #[DataProvider('getDataProviderWhenUserNotLogged')]
    public function testAccessPageWhenUserNotLogged(string $action, string $httpMethod, int $statusCode): void
    {
        $this->accessPage($httpMethod, $action, $statusCode);
    }

    private function accessPage(string $httpMethod, string $action, int $statusCode, ?string $email = null): void
    {
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

        $this->assertResponseStatusCodeSame($statusCode);
    }
}
