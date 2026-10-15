<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Kana;
use App\Enum\KanaType;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Kana>
 */
final class KanaFactory extends PersistentObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    #[\Override]
    public static function class(): string
    {
        return Kana::class;
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
            'ideogram' => self::faker()->text(10),
            'kunrei' => self::faker()->text(10),
            'position' => self::faker()->randomNumber(),
            'romaji' => self::faker()->text(10),
            'type' => self::faker()->randomElement(KanaType::cases()),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Kana $kana): void {})
        ;
    }
}
