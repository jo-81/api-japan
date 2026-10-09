<?php

declare(strict_types=1);

namespace App\Entity;

use App\ValueObject\Slug;
use Doctrine\ORM\Mapping as ORM;
use App\Model\SluggableInterface;
use App\Traits\TimestampableTrait;
use App\Repository\ThemeRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: ThemeRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'Cette valeur est déjà utilisée.')]
class Theme implements SluggableInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $name = null;

    #[ORM\Embedded(class: Slug::class, columnPrefix: false)]
    private ?Slug $slug = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSluggableFieldName(): string
    {
        return 'name';
    }

    public function getSluggableText(): string
    {
        return $this->name ?? '';
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
}
