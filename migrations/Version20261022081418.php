<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261022081418 extends AbstractMigration
{
    public function getDescription(): string
    {
        return "Ajout d'un index à meaning et des propriétés createdAt et updatedAt pour Signification.";
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE signification ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
        $this->addSql('CREATE INDEX IDX_321CFF863F31F002 ON signification (meaning)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_321CFF863F31F002 ON signification');
        $this->addSql('ALTER TABLE signification DROP created_at, DROP updated_at');
    }
}
