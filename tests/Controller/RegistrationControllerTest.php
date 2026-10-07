<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use App\Tests\Story\UniqueUserStory;
use Zenstruck\Foundry\Attribute\WithStory;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $container = static::getContainer();

        $this->userRepository = $container->get(UserRepository::class);
    }

    #[WithStory(UniqueUserStory::class)]
    #[DataProvider('getInvalidDataProvider')]
    public function testRegisterWithInvalidData(
        string $email,
        string $plainPassword,
        string $passwordConfirmation,
        string $message,
    ): void {
        $this->client->request('GET', '/inscription');
        $this->assertResponseIsSuccessful();

        $this->client->submitForm('Enregistrer', [
            'registration_form[email]' => $email,
            'registration_form[plainPassword]' => $plainPassword,
            'registration_form[passwordConfirmation]' => $passwordConfirmation,
        ]);

        $this->assertResponseIsUnprocessable();

        $content = $this->client->getResponse()->getContent();

        $this->assertStringContainsString(
            $message,
            !$content ? '' : $content,
        );
    }

    /**
     * @return array<string, array{0: string, 1: string, 2:string, 3:string}>
     */
    public static function getInvalidDataProvider(): array
    {
        return [
            'Email invalide' => [
                'test-registrationexample.fr',
                'X7!kP9@vR2#qL5',
                'X7!kP9@vR2#qL5',
                'Cette valeur doit être une adresse email valide.',
            ],

            'Email vide' => [
                '',
                'X7!kP9@vR2#qL5',
                'X7!kP9@vR2#qL5',
                'Ce champ ne peut pa être vide.',
            ],

            'Email déjà présent' => [
                'test-not-unique@example.com',
                'X7!kP9@vR2#qL5',
                'X7!kP9@vR2#qL5',
                'Cette adresse email est déjà utilisée.',
            ],

            'plainPassword et passwordConfirmation qui ne correspondent pas' => [
                'test-registration@example.fr',
                'X7!kP9@vR2#qL5',
                'X7!kP9@vR2#',
                'Les mots de passe ne sont pas identiques.',
            ],

            'plainPassword vide' => [
                'test-registration@example.fr',
                '',
                'X7!kP9@vR2#',
                'Ce champ ne peut pa être vide.',
            ],

            'plainPassword qui ne correspond pas à MEDIUM' => [
                'test-registration@example.fr',
                'Password123!',
                'Password123!',
                'La force du mot de passe est trop faible. Veuillez utiliser un mot de passe plus fort.',
            ],

            'plainPassword qui ne possède pas 8 caractères' => [
                'test-registration@example.fr',
                '1234567',
                '1234567',
                'Cette chaîne est trop courte. Elle doit contenir au minimum 8 caractères.',
            ],
        ];
    }

    public function testRegister(): void
    {
        $this->client->request('GET', '/inscription');
        $this->assertResponseIsSuccessful();

        $this->client->submitForm('Enregistrer', [
            'registration_form[email]' => 'test-registration@example.fr',
            'registration_form[plainPassword]' => 'X7!kP9@vR2#qL5',
            'registration_form[passwordConfirmation]' => 'X7!kP9@vR2#qL5',
        ]);

        $this->assertCount(1, $this->userRepository->findAll());
        $this->assertFalse($this->userRepository->findAll()[0]->isEmailVerified());

        $this->assertEmailCount(1);
        $this->assertCount(1, $messages = $this->getMailerMessages());

        $this->assertResponseRedirects('/connexion');
        $this->client->followRedirect();

        $this->assertSelectorTextContains('div', 'Votre compte a bien été créé. Vous avez reçu un lien pour valider celui-ci dans votre boite mail.');

        // Get the verification link from the email
        /** @var TemplatedEmail $templatedEmail */
        $templatedEmail = $messages[0];
        $messageBody = $templatedEmail->getHtmlBody();
        $this->assertIsString($messageBody);

        preg_match('#(http://localhost/verify/email.+)">#', $messageBody, $resetLink);

        $this->client->request('GET', $resetLink[1] ?? '');
        $this->client->followRedirect();

        $this->assertSelectorTextContains('div', 'Votre adresse email a bien été vérifiée.');

        $this->assertTrue(static::getContainer()->get(UserRepository::class)->findAll()[0]->isEmailVerified());
    }

    #[WithStory(UniqueUserStory::class)]
    public function testRedirectionWhenUserLogin(): void
    {
        $testUser = $this->userRepository->findOneByEmail('test-not-unique@example.com');
        if (!is_null($testUser)) {
            $this->client->loginUser($testUser);
        }

        $this->client->request('GET', '/inscription');

        $this->assertResponseRedirects('/');
        $this->client->followRedirect();

        $this->assertSelectorTextContains('div', 'Vous êtes déjà connecté.');
    }
}
