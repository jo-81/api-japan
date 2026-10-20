<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\JlptLevel;
use App\ValueObject\Slug;
use Doctrine\ORM\Mapping as ORM;
use App\Model\SluggableInterface;
use App\Traits\TimestampableTrait;
use App\Repository\VocabularyRepository;
use App\Validator\Constraints as AppAssert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['meaning'], message: 'Cette valeur est déjà utilisée.')]
#[ORM\Entity(repositoryClass: VocabularyRepository::class)]
class Vocabulary implements SluggableInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Length(min: 1, max: 255)]
    #[Assert\NoSuspiciousCharacters]
    #[ORM\Column(length: 255, index: true)]
    private ?string $meaning = null;

    #[ORM\Embedded(class: Slug::class, columnPrefix: false)]
    private ?Slug $slug = null;

    #[Assert\Length(min: 1, max: 255)]
    #[Assert\NoSuspiciousCharacters]
    #[ORM\Column(length: 255)]
    private ?string $romaji = null;

    #[Assert\Length(min: 1, max: 255)]
    #[Assert\NoSuspiciousCharacters]
    #[ORM\Column(length: 255)]
    private ?string $kunrei = null;

    #[Assert\Length(min: 1, max: 255)]
    #[Assert\NoSuspiciousCharacters]
    #[AppAssert\KanaIdeogramCode]
    #[ORM\Column(length: 255)]
    private ?string $kana = null;

    #[ORM\Column]
    private bool $published = false;

    #[ORM\ManyToOne(inversedBy: 'vocabularies')]
    private ?Theme $theme = null;

    #[ORM\Column(nullable: true, enumType: JlptLevel::class)]
    private ?JlptLevel $jlpt = null;

    /**
     * @var Collection<int, Kanji>
     */
    #[ORM\ManyToMany(targetEntity: Kanji::class, mappedBy: 'words')]
    private Collection $kanjis;

    public function __construct()
    {
        $this->kanjis = new ArrayCollection();
    }

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

    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    public function setTheme(?Theme $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    public function getRomaji(): ?string
    {
        return $this->romaji;
    }

    public function setRomaji(string $romaji): static
    {
        $this->romaji = $romaji;

        return $this;
    }

    public function getKunrei(): ?string
    {
        return $this->kunrei;
    }

    public function setKunrei(string $kunrei): static
    {
        $this->kunrei = $kunrei;

        return $this;
    }

    public function getKana(): ?string
    {
        return $this->kana;
    }

    public function setKana(string $kana): static
    {
        $this->kana = $kana;

        return $this;
    }

    public function isPublished(): bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): static
    {
        $this->published = $published;

        return $this;
    }

    public function getJlpt(): ?JlptLevel
    {
        return $this->jlpt;
    }

    public function setJlpt(?JlptLevel $jlpt): static
    {
        $this->jlpt = $jlpt;

        return $this;
    }

    public function getSluggableFieldName(): string
    {
        return 'meaning';
    }

    public function getSluggableText(): string
    {
        return $this->meaning ?? '';
    }

    public function getSlug(): ?Slug
    {
        return $this->slug;
    }

    public function setSlug(Slug $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function __toString(): string
    {
        return $this->meaning ?? '';
    }

    /**
     * @return Collection<int, Kanji>
     */
    public function getKanjis(): Collection
    {
        return $this->kanjis;
    }

    public function addKanji(Kanji $kanji): static
    {
        if (!$this->kanjis->contains($kanji)) {
            $this->kanjis->add($kanji);
            $kanji->addWord($this);
        }

        return $this;
    }

    public function removeKanji(Kanji $kanji): static
    {
        if ($this->kanjis->removeElement($kanji)) {
            $kanji->removeWord($this);
        }

        return $this;
    }
}
