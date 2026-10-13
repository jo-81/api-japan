<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Entity\User;
use App\Entity\ApiKey;
use App\Enum\ApiKeyStatus;
use App\Story\ApiKeyStory;
use App\Factory\ApiKeyFactory;
use App\Tests\Utils\CrudTestCase;
use App\Tests\Story\UserLoginStory;
use App\Repository\ApiKeyRepository;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Foundry\Attribute\WithStory;
use App\Controller\Admin\DashboardController;
use App\Controller\Admin\ApiKeyCrudController;

#[WithStory(UserLoginStory::class)]
#[WithStory(ApiKeyStory::class)]
class ApiKeyCrudControllerTest extends CrudTestCase
{
    use ResetDatabase;
    use Factories;

    /**
     * @return class-string<ApiKeyCrudController> returns the tested Controller Fqcn
     */
    protected function getControllerFqcn(): string
    {
        return ApiKeyCrudController::class;
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

    public function testRevokedApiKeyWhenUserNotLogged(): void
    {
        $apikey = $this->getApiKey();
        $url = $this->adminUrlGenerator
            ->setRoute('admin_api_key_revoked', ['id' => $apikey->getId()])
            ->generateUrl()
        ;

        $this->client->request('GET', $url);

        self::assertResponseRedirects();
    }

    public function testRevokedApiKeyWhenUserLoggedWithRoleUser(): void
    {
        $user = $this->getUserLogged(['email' => 'user@example.com']);
        self::assertNotNull($user);

        $apikey = $this->getApiKey();
        $url = $this->adminUrlGenerator
            ->setRoute('admin_api_key_revoked', ['id' => $apikey->getId()])
            ->generateUrl()
        ;

        $this->client->loginUser($user);
        $this->client->request('GET', $url);

        self::assertResponseStatusCodeSame(403);
    }

    public function testRevokedApiKey(): void
    {
        $user = $this->getUserLogged(['email' => 'admin@example.com']);
        self::assertNotNull($user);

        $apikey = $this->getApiKey();
        $apikeyId = $apikey->getId();
        self::assertNotNull($apikeyId);

        $url = $this->adminUrlGenerator
            ->setRoute('admin_api_key_revoked', ['id' => $apikeyId])
            ->generateUrl()
        ;

        $this->client->loginUser($user);
        $this->client->request('GET', $url);

        /** @var ApiKey */
        $revokedApikey = static::getContainer()->get(ApiKeyRepository::class)->find($apikeyId);

        self::assertSame(ApiKeyStatus::REVOKED, $revokedApikey->getStatus());
        self::assertResponseRedirects($this->generateDetailUrl($apikeyId));
    }

    private function getApiKey(): ApiKey
    {
        $userRepository = $this->entityManager->getRepository(User::class);

        return ApiKeyFactory::createOne([
            'user' => $userRepository->findOneBy(['email' => 'user@example.com']),
            'status' => ApiKeyStatus::ACTIVE,
        ]);
    }
}
