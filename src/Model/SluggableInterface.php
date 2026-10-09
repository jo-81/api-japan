<?php

declare(strict_types=1);

namespace App\Model;

use App\ValueObject\Slug;

interface SluggableInterface
{
    /**
     * Retourne le nom de la propriété dont la valeur doit être convertie en slug (ex: 'name', 'title').
     */
    public function getSluggableFieldName(): string;

    /**
     * Retourne la valeur à convertir en slug.
     */
    public function getSluggableText(): string;

    public function getSlug(): ?Slug;

    public function setSlug(Slug $slug): static;
}
