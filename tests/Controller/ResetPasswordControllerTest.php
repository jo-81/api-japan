<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\Story\UserLoginStory;
use Zenstruck\Foundry\Test\Factories;
use Doctrine\ORM\EntityManagerInterface;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Foundry\Attribute\WithStory;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use App\Repository\ResetPasswordRequestRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[WithStory(UserLoginStory::class)]
class ResetPasswordControllerTest extends WebTestCase
{
    use ResetDatabase;
    use Factories;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testResetPassword(): void
    {
        // Test Request reset password page
        $this->client->request('GET', '/reinitialisation-mot-de-passe');
        self::assertResponseIsSuccessful();

        $this->client->enableProfiler();

        // Submit the reset password form and test email message is queued / sent
        $this->client->submitForm('Envoyer la demande', [
            'reset_password_request_form[email]' => 'admin@example.com',
        ]);

        // Ensure the reset password email was sent
        self::assertEmailCount(1);

        $messages = $this->getMailerMessages();
        self::assertEmailAddressContains($messages[0], 'from', 'mailer@your-domain.com');
        self::assertEmailAddressContains($messages[0], 'to', 'admin@example.com');
        self::assertEmailTextBodyContains($messages[0], 'Ce lien expirera dans 1 heure.');

        self::assertResponseRedirects('/reinitialisation-mot-de-passe/verification-email');

        // Test check email landing page shows correct "expires at" time
        $crawler = $this->client->followRedirect();

        self::assertPageTitleContains('E-mail de réinitialisation du mot de passe envoyé');
        self::assertStringContainsString('Ce lien expirera dans 1 heure.', $crawler->html());

        // Test the link sent in the email is valid
        /** @var array<TemplatedEmail> $messages */
        $email = $messages[0]->getTextBody();
        self::assertIsString($email);
        preg_match('#(/reinitialisation-mot-de-passe/reinitialiser/[a-zA-Z0-9]+)#', $email, $resetLink);

        $this->client->request('GET', $resetLink[1]); // @phpstan-ignore-line

        self::assertResponseRedirects('/reinitialisation-mot-de-passe/reinitialiser');

        $this->client->followRedirect();

        // Test we can set a new password
        $this->client->submitForm('Réinitialisez', [
            'change_password_form[plainPassword][first]' => '136546X7!kedfzgeP9@vR2#qL511',
            'change_password_form[plainPassword][second]' => '136546X7!kedfzgeP9@vR2#qL511',
        ]);

        self::assertResponseRedirects('/');

        $container = static::getContainer();
        $container->get(EntityManagerInterface::class)->clear();

        $userRepository = $container->get(UserRepository::class);
        $resetPasswordRequestRepository = $container->get(ResetPasswordRequestRepository::class);

        $user = $userRepository->findOneBy(['email' => 'admin@example.com']);
        self::assertInstanceOf(User::class, $user);

        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = static::getContainer()->get(UserPasswordHasherInterface::class);
        self::assertTrue($passwordHasher->isPasswordValid($user, '136546X7!kedfzgeP9@vR2#qL511'));

        self::assertCount(0, $resetPasswordRequestRepository->findBy(['user' => $user]));
    }

    public function testResetPasswordWhenUserNotLogged(): void
    {
        $container = static::getContainer();
        $userRepository = $container->get(UserRepository::class);

        $user = $userRepository->findOneByEmail('admin@example.com');
        if (!is_null($user)) {
            $this->client->loginUser($user);
        }

        $this->client->request('GET', '/reinitialisation-mot-de-passe');
        $this->assertResponseRedirects('/');
        $this->client->followRedirect();
    }
}
