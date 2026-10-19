<?php

declare(strict_types=1);

namespace App\Entity;

use App\ValueObject\Slug;
use Doctrine\ORM\Mapping as ORM;
use App\Model\SluggableInterface;
use App\Traits\TimestampableTrait;
use App\Repository\ThemeRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
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

    /**
     * @var Collection<int, Vocabulary>
     */
    #[ORM\OneToMany(targetEntity: Vocabulary::class, mappedBy: 'theme')]
    private Collection $vocabularies;

    public function __construct()
    {
        $this->vocabularies = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Vocabulary>
     */
    public function getVocabularies(): Collection
    {
        return $this->vocabularies;
    }

    public function addVocabulary(Vocabulary $vocabulary): static
    {
        if (!$this->vocabularies->contains($vocabulary)) {
            $this->vocabularies->add($vocabulary);
            $vocabulary->setTheme($this);
        }

        return $this;
    }

    public function removeVocabulary(Vocabulary $vocabulary): static
    {
        if ($this->vocabularies->removeElement($vocabulary)) {
            // set the owning side to null (unless already changed)
            if ($vocabulary->getTheme() === $this) {
                $vocabulary->setTheme(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }
}
