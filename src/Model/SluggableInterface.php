<?php

declare(strict_types=1);

namespace App\Model;

use App\ValueObject\Slug;

interface SluggableInterface
{
    /**
     * Retourne la valeur à convertir en slug (ex: name, title).
     */
    public function getSluggableText(): string;

    public function getSlug(): ?Slug;

    public function setSlug(Slug $slug): static;
}
