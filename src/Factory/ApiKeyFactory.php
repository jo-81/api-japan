<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\ApiKey;
use App\Enum\ApiKeyStatus;
use App\Service\ApiKeyManager;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<ApiKey>
 */
final class ApiKeyFactory extends PersistentObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(private ApiKeyManager $apiKeyManager)
    {
    }

    #[\Override]
    public static function class(): string
    {
        return ApiKey::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->word(),
            'status' => self::faker()->randomElement(ApiKeyStatus::cases()),
            'user' => UserFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this->afterInstantiate(function (ApiKey $apiKey): void {
            $plainToken = bin2hex(random_bytes(32));
            $apiKey->setToken($this->apiKeyManager->hashToken($plainToken));
        });
    }
}
