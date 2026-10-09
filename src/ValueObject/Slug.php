<?php

declare(strict_types=1);

namespace App\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
final class Slug implements \Stringable
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(name: 'slug', length: 255, unique: true, nullable: true)]
    private ?string $value = null;

    public function __construct(?string $value = null)
    {
        if (null !== $value) {
            $this->validate($value);
            $this->value = $value;
        }
    }

    public static function fromText(string $text, SluggerInterface $slugger): self
    {
        $slugified = (string) $slugger->slug($text)->lower();

        return new self($slugified);
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    private function validate(string $value): void
    {
        if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value)) {
            throw new \InvalidArgumentException(sprintf('Le slug "%s" n\'est pas valide.', $value));
        }
    }

    public function __toString(): string
    {
        return $this->value ?? '';
    }
}
