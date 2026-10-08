<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use App\Tests\Story\UserLoginStory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Foundry\Attribute\WithStory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

#[WithStory(UserLoginStory::class)]
class LoginControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    private KernelBrowser $client;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $container = static::getContainer();

        $this->userRepository = $container->get(UserRepository::class);
    }

    public function testLogin(): void
    {
        $this->client->request('GET', '/connexion');
        $this->assertResponseIsSuccessful();
    }

    public function testLoginWithEmailIsVerified(): void
    {
        $this->client->request('GET', '/connexion');

        $this->client->submitForm('Connexion', [
            '_username' => 'admin@example.com',
            '_password' => 'X7!kP9@vR2#qL5',
        ]);

        $this->assertResponseRedirects('/');
        $this->client->followRedirect();

        $this->assertSelectorTextContains('div', 'Bienvenue !');
    }

    public function testLoginWithEmailIsNotVerified(): void
    {
        $this->client->request('GET', '/connexion');

        $this->client->submitForm('Connexion', [
            '_username' => 'email-not-verified@example.com',
            '_password' => 'X7!kP9@vR2#qL5',
        ]);

        $this->assertResponseRedirects('/connexion');
        $this->client->followRedirect();

        $this->assertSelectorTextContains('div', "Votre compte n'a pas encore été vérifié.");
    }

    public function testRedirectionAccessPageLoginWhenUserLogged(): void
    {
        $user = $this->userRepository->findOneBy(['email' => 'admin@example.com']);
        if (!is_null($user)) {
            $this->client->loginUser($user);
        }

        $this->client->request('GET', '/connexion');
        $this->assertResponseRedirects('/');
        $this->client->followRedirect();

        $this->assertSelectorTextContains('div', 'Vous êtes déjà connecté.');
    }
}
