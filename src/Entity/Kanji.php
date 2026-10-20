<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\JlptLevel;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\TimestampableTrait;
use App\Repository\KanjiRepository;
use App\Validator\Constraints as AppAssert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[UniqueEntity(fields: 'ideogram')]
#[UniqueEntity(fields: 'position')]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: KanjiRepository::class)]
class Kanji
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NoSuspiciousCharacters()]
    #[Assert\Length(exactly: 1)]
    #[AppAssert\Kanji()]
    #[ORM\Column(length: 10, unique: true)]
    private ?string $ideogram = null;

    #[Assert\Positive()]
    #[ORM\Column(unique: true, nullable: true)]
    private ?int $position = null;

    #[Assert\Positive()]
    #[ORM\Column]
    private ?int $numberStroke = null;

    #[ORM\Column]
    private ?bool $published = null;

    #[ORM\Column(nullable: true, enumType: JlptLevel::class)]
    private ?JlptLevel $jlpt = null;

    /** @var string[] $meanings */
    #[Assert\All([
        new Assert\NotBlank(),
        new AppAssert\OnlyString(),
        new Assert\NoSuspiciousCharacters(),
    ])]
    #[ORM\Column]
    private array $meanings = [];

    /** @var string[] $kunyomi */
    #[Assert\All([
        new AppAssert\OnlyString(),
        new Assert\NoSuspiciousCharacters(),
    ])]
    #[ORM\Column(nullable: true)]
    private ?array $kunyomi = null;

    /** @var string[] $onyomi */
    #[Assert\All([
        new AppAssert\OnlyString(),
        new Assert\NoSuspiciousCharacters(),
    ])]
    #[ORM\Column(nullable: true)]
    private ?array $onyomi = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\ManyToMany(targetEntity: self::class)]
    private Collection $similars;

    /**
     * @var Collection<int, Vocabulary>
     */
    #[ORM\ManyToMany(targetEntity: Vocabulary::class, inversedBy: 'kanjis')]
    private Collection $words;

    public function __construct()
    {
        $this->similars = new ArrayCollection();
        $this->words = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdeogram(): ?string
    {
        return $this->ideogram;
    }

    public function setIdeogram(string $ideogram): static
    {
        $this->ideogram = $ideogram;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getNumberStroke(): ?int
    {
        return $this->numberStroke;
    }

    public function setNumberStroke(int $numberStroke): static
    {
        $this->numberStroke = $numberStroke;

        return $this;
    }

    public function isPublished(): ?bool
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

    /**
     * @return string[]
     */
    public function getMeanings(): array
    {
        return $this->meanings;
    }

    /**
     * @param string[] $meanings
     */
    public function setMeanings(array $meanings): static
    {
        $this->meanings = $meanings;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getKunyomi(): ?array
    {
        return $this->kunyomi;
    }

    /**
     * @param string[]|null $kunyomi
     */
    public function setKunyomi(?array $kunyomi): static
    {
        $this->kunyomi = $kunyomi;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getOnyomi(): ?array
    {
        return $this->onyomi;
    }

    /**
     * @param string[]|null $onyomi
     */
    public function setOnyomi(?array $onyomi): static
    {
        $this->onyomi = $onyomi;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getSimilars(): Collection
    {
        return $this->similars;
    }

    public function addSimilar(self $similar): static
    {
        if (!$this->similars->contains($similar)) {
            $this->similars->add($similar);
        }

        return $this;
    }

    public function removeSimilar(self $similar): static
    {
        $this->similars->removeElement($similar);

        return $this;
    }

    /**
     * @return Collection<int, Vocabulary>
     */
    public function getWords(): Collection
    {
        return $this->words;
    }

    public function addWord(Vocabulary $word): static
    {
        if (!$this->words->contains($word)) {
            $this->words->add($word);
        }

        return $this;
    }

    public function removeWord(Vocabulary $word): static
    {
        $this->words->removeElement($word);

        return $this;
    }
}
