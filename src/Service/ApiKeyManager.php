<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Entity\ApiKey;
use App\Enum\ApiKeyStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ApiKeyManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        #[Autowire(param: 'api_key.max_active_keys')]
        private readonly int $maxActiveKeys,
        #[Autowire(param: 'api_key.default_expiration')]
        private readonly string $defaultExpiration,
    ) {
    }

    /**
     * Génère un token haché à partir d'un token en clair.
     */
    public function hashToken(string $plainToken): string
    {
        return hash('sha256', $plainToken);
    }

    /**
     * Génère une nouvelle clé API pour un utilisateur.
     *
     * @return array{plainToken: string, apiKey: ApiKey}
     *
     * @throws \RuntimeException si la limite de clés est atteinte
     */
    public function generate(User $user, string $name): array
    {
        // 1. Vérification de l'unicité du nom pour cet utilisateur
        $exists = $this->entityManager->getRepository(ApiKey::class)->findOneBy([
            'user' => $user,
            'name' => $name,
        ]);

        if ($exists) {
            throw new \RuntimeException(sprintf('Vous possédez déjà une clé API nommée "%s".', $name));
        }

        // 2. Limitation du nombre de clés actives par utilisateur
        $activeKeysCount = $this->entityManager->getRepository(ApiKey::class)->count([
            'user' => $user,
            'status' => ApiKeyStatus::ACTIVE,
        ]);

        if ($activeKeysCount >= $this->maxActiveKeys) {
            throw new \RuntimeException(sprintf('Vous avez atteint le nombre maximum de clés API actives (%d).', $this->maxActiveKeys));
        }

        // 2. Génération d'un token aléatoire sécurisé (64 caractères hex)
        $plainToken = bin2hex(random_bytes(32));

        // 3. Création de l'entité avec le hash du token
        $apiKey = new ApiKey();
        $apiKey->setUser($user);
        $apiKey->setName($name);
        $apiKey->setToken($this->hashToken($plainToken));
        $apiKey->setStatus(ApiKeyStatus::ACTIVE);

        // Expiration basée sur la configuration
        $apiKey->setExpiredAt(new \DateTimeImmutable($this->defaultExpiration));

        $this->entityManager->persist($apiKey);
        $this->entityManager->flush();

        return [
            'plainToken' => $plainToken,
            'apiKey' => $apiKey,
        ];
    }

    /**
     * Vérifie la validité d'un token et retourne l'utilisateur associé.
     */
    public function verify(string $plainToken): ?User
    {
        $hashedToken = $this->hashToken($plainToken);

        $apiKey = $this->entityManager->getRepository(ApiKey::class)->findOneBy([
            'token' => $hashedToken,
        ]);

        if (!$apiKey) {
            return null;
        }

        // Vérification du status (ACTIVE)
        if (!$apiKey->getStatus()?->isUsable()) {
            return null;
        }

        // Vérification de l'expiration
        if (null !== $apiKey->getExpiredAt() && $apiKey->getExpiredAt() < new \DateTimeImmutable()) {
            // On pourrait ici passer le status à EXPIRED automatiquement
            return null;
        }

        // Mise à jour de la date de dernière utilisation
        $apiKey->setLastUsedAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        return $apiKey->getUser();
    }

    /**
     * Révoque une clé API.
     */
    public function revoke(ApiKey $apiKey): void
    {
        if (ApiKeyStatus::ACTIVE !== $apiKey->getStatus()) {
            return;
        }

        $apiKey->setStatus(ApiKeyStatus::REVOKED);
        $this->entityManager->flush();
    }
}
