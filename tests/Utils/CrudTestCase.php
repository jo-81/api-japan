<?php

declare(strict_types=1);

namespace App\Tests\Utils;

use App\Entity\User;
use Symfony\Component\Uid\UuidV7;
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

    // #[DataProvider('getDataProviderWhenUserLogged')]
    // public function testAccessPageWhenUserLogged(string $action, string $httpMethod, int $statusCode): void
    // {
    //     $user = $this->getUserLogged(['email' => 'email@example.com']);
    //     $entityId = 1;

    //     if (!is_null($user)) {
    //         $this->client->loginUser($user);
    //     }

    //     $this->client->request($httpMethod, $this->getAdminLink($action, $entityId));

    //     static::assertResponseStatusCodeSame($statusCode);
    // }

    // #[DataProvider('getDataProviderWhenUserNotLogged')]
    // public function testAccessPageWhenUserNotLogged(string $action, string $httpMethod, int $statusCode): void
    // {
    //     $this->client->request($httpMethod, $this->getAdminLink($action));

    //     static::assertResponseStatusCodeSame($statusCode);
    // }
}
