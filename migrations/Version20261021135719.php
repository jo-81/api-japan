<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261021135719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return "Création de l'entité Signification et modification de l'entité Kanji";
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE signification (id INT AUTO_INCREMENT NOT NULL, meaning VARCHAR(255) NOT NULL, onyumi VARCHAR(255) DEFAULT NULL, kunyomi VARCHAR(255) DEFAULT NULL, kanji_id INT NOT NULL, INDEX IDX_321CFF86FB3081B8 (kanji_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE signification ADD CONSTRAINT FK_321CFF86FB3081B8 FOREIGN KEY (kanji_id) REFERENCES kanji (id)');
        $this->addSql('ALTER TABLE kanji DROP meanings, DROP kunyomi, DROP onyomi');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE signification DROP FOREIGN KEY FK_321CFF86FB3081B8');
        $this->addSql('DROP TABLE signification');
        $this->addSql('ALTER TABLE kanji ADD meanings JSON NOT NULL, ADD kunyomi JSON DEFAULT NULL, ADD onyomi JSON DEFAULT NULL');
    }
}
