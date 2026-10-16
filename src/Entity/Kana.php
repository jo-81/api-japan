<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\KanaType;
use App\Enum\KanaAccentType;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\KanaRepository;
use App\Traits\TimestampableTrait;
use App\Validator\Constraints as AppAssert;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Attribute as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[Vich\Uploadable]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: KanaRepository::class)]
#[UniqueEntity(
    fields: 'ideogram',
    message: 'L\'idéogramme {{ value }} existe déjà.',
)]
#[UniqueEntity(
    fields: 'position',
    message: 'La position {{ value }} est déjà attribué à un kana.',
)]
class Kana
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Length(max: 10)]
    #[Assert\NotBlank]
    #[ORM\Column(length: 10)]
    private ?string $romaji = null;

    #[Assert\Length(max: 10)]
    #[Assert\NotBlank]
    #[AppAssert\KanaIdeogramCode]
    #[ORM\Column(length: 10, unique: true)]
    private ?string $ideogram = null;

    #[Assert\Length(max: 10)]
    #[Assert\NotBlank]
    #[ORM\Column(length: 10)]
    private ?string $kunrei = null;

    #[Assert\Positive]
    #[ORM\Column]
    private ?int $position = null;

    #[Assert\NotBlank]
    #[ORM\Column(enumType: KanaType::class)]
    private ?KanaType $type = null;

    #[ORM\Column(nullable: true, enumType: KanaAccentType::class)]
    private ?KanaAccentType $accentType = null;

    #[Assert\File(
        mimeTypes: ['image/svg+xml'],
        maxSize: '500k',
        mimeTypesMessage: 'Le fichier doit être un fichier de type svg.',
        maxSizeMessage: 'Le fichier est trop volumineux (max 500k).',
    )]
    #[Vich\UploadableField(mapping: 'kana', fileNameProperty: 'imageName', size: 'imageSize')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?int $imageSize = null;

    /**
     * @var Collection<int, Kana>
     */
    #[Assert\When(
        expression: 'this.hasCombination()',
        constraints: [
            new Assert\Count(exactly: 2),
        ],
    )]
    #[ORM\ManyToMany(targetEntity: self::class)]
    #[ORM\JoinTable(name: 'kana_combinations')]
    private Collection $components;

    public function __construct()
    {
        $this->components = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIdeogram(): ?string
    {
        return $this->ideogram;
    }

    public function setIdeogram(string $ideogram): static
    {
        $this->ideogram = $ideogram;

        return $this;
    }

    public function hasCombination(): bool
    {
        return !$this->components->isEmpty();
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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getType(): ?KanaType
    {
        return $this->type;
    }

    public function setType(KanaType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getAccentType(): ?KanaAccentType
    {
        return $this->accentType;
    }

    public function setAccentType(?KanaAccentType $accentType): static
    {
        $this->accentType = $accentType;

        return $this;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    /**
     * @return Collection<int, Kana>
     */
    public function getComponents(): Collection
    {
        return $this->components;
    }

    public function addComponent(Kana $component): static
    {
        if (!$this->components->contains($component)) {
            $this->components->add($component);
        }

        return $this;
    }

    public function removeComponent(Kana $component): static
    {
        $this->components->removeElement($component);

        return $this;
    }

    public function __toString(): string
    {
        return $this->ideogram ?? '';
    }
}
