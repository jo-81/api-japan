<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Doctrine\ORM\Events;
use App\ValueObject\Slug;
use App\Model\SluggableInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;

#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
class SluggableListener
{
    public function __construct(
        private readonly SluggerInterface $slugger,
    ) {
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $this->processSlug($args->getObject());
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $this->processSlug($args->getObject());
    }

    private function processSlug(object $entity): void
    {
        if (!$entity instanceof SluggableInterface) {
            return;
        }

        $textToSlug = $entity->getSluggableText();

        if (!empty($textToSlug)) {
            $entity->setSlug(new Slug((string) $this->slugger->slug($textToSlug)->lower()));
        }
    }
}
