<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;
use App\Entity\ApiKey;
use App\Tests\Story\UserLoginStory;
use Doctrine\ORM\EntityManagerInterface;
use Zenstruck\Foundry\Attribute\WithStory;
use Zenstruck\Foundry\Attribute\ResetDatabase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

#[ResetDatabase]
#[WithStory(UserLoginStory::class)]
final class ApiKeyControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $container = static::getContainer();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get(EntityManagerInterface::class);
        $this->entityManager = $entityManager;
    }

    public function testCannotGenerateDuplicateApiKeyName(): void
    {
        $name = 'projet unique';

        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'admin@example.com'])
        ;
        self::assertNotNull($user);
        $this->client->loginUser($user);

        // Première génération
        $this->client->request('GET', '/profile/apikeys');
        $this->client->submitForm('Générer', [
            'api_key_dto[name]' => $name,
        ]);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('div', 'Votre clé a bien été générée.');

        // Deuxième génération avec le même nom
        $this->client->request('GET', '/profile/apikeys');
        $this->client->submitForm('Générer', [
            'api_key_dto[name]' => $name,
        ]);
        $this->client->followRedirect();

        $this->assertSelectorTextContains('div', sprintf('Vous possédez déjà une clé API nommée "%s".', $name));
    }

    public function testIndexWhenUserNotLogged(): void
    {
        $this->client->request('GET', '/profile/apikeys');
        self::assertResponseRedirects('/connexion');
    }

    public function testIndexWhenUserLogged(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'admin@example.com'])
        ;
        self::assertNotNull($user);

        $this->client->loginUser($user);
        $this->client->request('GET', '/profile/apikeys');
        self::assertResponseIsSuccessful();
    }

    public function testGenerateApiKey(): void
    {
        $name = 'mon projet wordpress';

        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'admin@example.com'])
        ;
        self::assertNotNull($user);

        $this->client->loginUser($user);
        $this->client->request('GET', '/profile/apikeys');
        $this->client->submitForm('Générer', [
            'api_key_dto[name]' => $name,
        ]);

        $apikey = $this->entityManager->getRepository(ApiKey::class)->findOneBy(['name' => $name]);
        self::assertInstanceOf(ApiKey::class, $apikey);

        // Vérification sécurité : le token en base doit être un hash (64 chars pour sha256)
        self::assertNotNull($apikey->getToken());
        self::assertEquals(64, strlen($apikey->getToken()));

        self::assertResponseRedirects();
        $this->client->followRedirect();

        $this->assertSelectorTextContains('div', 'Votre clé a bien été générée.');

        // Vérification que le token est bien affiché dans le message flash
        /** @var string */
        $content = $this->client->getResponse()->getContent();
        // Le token fait 64 caractères (bin2hex de 32 bytes)
        self::assertMatchesRegularExpression('/[a-f0-9]{64}/', $content);
    }

    public function testCannotGenerateMoreThanMaxKeys(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'admin@example.com'])
        ;
        self::assertNotNull($user);
        $this->client->loginUser($user);

        // On crée des clés jusqu'à ce que ça bloque
        for ($i = 0; $i < 20; ++$i) {
            $this->client->request('GET', '/profile/apikeys');
            $this->client->submitForm('Générer', [
                'api_key_dto[name]' => "Key $i",
            ]);

            /** @var string */
            $content = $this->client->getResponse()->getContent();
            if (str_contains($content, 'nombre maximum de clés API actives')) {
                break;
            }
        }

        $this->client->followRedirect();
        $this->assertSelectorTextContains('body', 'Vous avez atteint le nombre maximum de clés API actives');
    }

    public function testGenerateApiKeyWithInvalidData(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'admin@example.com'])
        ;
        self::assertNotNull($user);
        $this->client->loginUser($user);

        $this->client->request('GET', '/profile/apikeys');
        // Nom vide pour déclencher NotBlank
        $this->client->submitForm('Générer', [
            'api_key_dto[name]' => '',
        ]);

        // On vérifie que nous sommes toujours sur la page (on ne a pas été redirigé)
        $this->assertSelectorTextContains('ul', 'Cette valeur ne doit pas être vide.');
    }
}
