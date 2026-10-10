<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Entity\Theme;
use App\Factory\ThemeFactory;
use App\Tests\Utils\CrudTestCase;
use App\Repository\ThemeRepository;
use App\Tests\Story\ThemeTestStory;
use App\Tests\Story\UserLoginStory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Foundry\Attribute\WithStory;
use App\Controller\Admin\DashboardController;
use App\Controller\Admin\ThemeCrudController;
use PHPUnit\Framework\Attributes\DataProvider;

#[WithStory(UserLoginStory::class)]
#[WithStory(ThemeTestStory::class)]
class ThemeCrudControllerTest extends CrudTestCase
{
    use ResetDatabase;
    use Factories;

    /**
     * @return class-string<ThemeCrudController> returns the tested Controller Fqcn
     */
    protected function getControllerFqcn(): string
    {
        return ThemeCrudController::class;
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
            ['new', 'GET', 200],
            ['edit', 'GET', 200],
        ];
    }

    public function testRegisterTheme(): void
    {
        $user = $this->getUserLogged(['email' => 'admin@example.com']);
        if (!is_null($user)) {
            $this->client->loginUser($user);
        }

        $this->client->request('GET', $this->getAdminLink('new'));
        $this->client->submitForm('ea[newForm][btn]', [
            'Theme[name]' => 'Un thème à voir',
        ]);

        /** @var ?Theme */
        $theme = static::getContainer()->get(ThemeRepository::class)->findOneBy(['name' => 'Un thème à voir']);

        $this->assertResponseRedirects($this->getAdminLink('detail', $theme?->getId())); // @phpstan-ignore-line

        $this->assertSame('un-theme-a-voir', $theme?->getSlug()?->getValue());
        $this->assertInstanceOf(Theme::class, $theme);
    }

    #[DataProvider('getDataProviderInvalidNameTheme')]
    public function testRegisterThemeWithInvalidName(string $value, string $message): void
    {
        $user = $this->getUserLogged(['email' => 'admin@example.com']);
        if (!is_null($user)) {
            $this->client->loginUser($user);
        }

        $this->client->request('GET', $this->getAdminLink('new'));
        $this->client->submitForm('ea[newForm][btn]', [
            'Theme[name]' => $value,
        ]);

        $this->assertResponseIsUnprocessable();

        $content = $this->client->getResponse()->getContent();

        $this->assertStringContainsString(
            $message,
            !$content ? '' : $content,
        );

        /** @var ?Theme */
        $theme = static::getContainer()->get(ThemeRepository::class)->findOneBy(['name' => 'Un thème à voir']);
        $this->assertNull($theme);
    }

    /**
     * @return array<int, array{0: string, 1: string}>
     */
    public static function getDataProviderInvalidNameTheme(): array
    {
        return [
            ['', 'Cette valeur ne doit pas être vide.'],
            [str_repeat('a', 249).'example', 'Cette chaîne est trop longue. Elle doit contenir au maximum 255 caractères.'],
            ['un theme spécifique', 'Cette valeur est déjà utilisée.'],
        ];
    }

    public function testEditTheme(): void
    {
        $theme = ThemeFactory::createOne([
            'name' => 'theme a édité',
        ]);

        $themeId = $theme->getId();
        $this->assertNotNull($themeId);

        $user = $this->getUserLogged(['email' => 'admin@example.com']);
        $this->assertNotNull($user, 'L\'utilisateur n\'a pas pu être récupéré.');
        $this->client->loginUser($user);

        $this->client->request('GET', $this->getAdminLink('edit', $themeId));
        $this->client->submitForm('ea[newForm][btn]', [
            'Theme[name]' => 'Un thème édité',
        ]);

        $this->assertResponseRedirects($this->getAdminLink('detail', $themeId));

        /** @var ?Theme $themeEdit */
        $themeEdit = static::getContainer()->get(ThemeRepository::class)->findOneBy(['id' => $themeId]);
        $this->assertNotNull($themeEdit);

        $slug = $themeEdit->getSlug();
        $this->assertNotNull($slug);

        $this->assertSame('un-theme-edite', $slug->getValue());
        $this->assertSame('Un thème édité', $themeEdit->getName());
        $this->assertSame($themeId, $themeEdit->getId());
    }
}
