<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\SignificationRepository;

#[ORM\Entity(repositoryClass: SignificationRepository::class)]
class Signification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $meaning = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $onyomi = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $kunyomi = null;

    #[ORM\ManyToOne(inversedBy: 'significations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Kanji $kanji = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMeaning(): ?string
    {
        return $this->meaning;
    }

    public function setMeaning(string $meaning): static
    {
        $this->meaning = $meaning;

        return $this;
    }

    public function getOnyomi(): ?string
    {
        return $this->onyomi;
    }

    public function setOnyomi(?string $onyomi): static
    {
        $this->onyomi = strtoupper($onyomi);

        return $this;
    }

    public function getKunyomi(): ?string
    {
        return $this->kunyomi;
    }

    public function setKunyomi(?string $kunyomi): static
    {
        $this->kunyomi = $kunyomi;

        return $this;
    }

    public function getKanji(): ?Kanji
    {
        return $this->kanji;
    }

    public function setKanji(?Kanji $kanji): static
    {
        $this->kanji = $kanji;

        return $this;
    }

    public function __toString(): string
    {
        return $this->meaning ?? '';
    }
}
