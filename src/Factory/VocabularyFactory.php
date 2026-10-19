<?php

declare(strict_types=1);

namespace App\Factory;

use App\Enum\JlptLevel;
use App\Entity\Vocabulary;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Vocabulary>
 */
final class VocabularyFactory extends PersistentObjectFactory
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
        return Vocabulary::class;
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
            'kana' => 'あ',
            'kunrei' => self::faker()->word(),
            'meaning' => self::faker()->unique()->word(),
            'published' => self::faker()->boolean(),
            'romaji' => self::faker()->word(),
            'jlpt' => self::faker()->randomElement(JlptLevel::cases()),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Vocabulary $vocabulary): void {})
        ;
    }
}
