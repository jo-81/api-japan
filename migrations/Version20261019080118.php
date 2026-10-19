<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261019080118 extends AbstractMigration
{
    public function getDescription(): string
    {
        return "Création de l'entité Vocabulary";
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE vocabulary (id INT AUTO_INCREMENT NOT NULL, meaning VARCHAR(255) NOT NULL, romaji VARCHAR(255) NOT NULL, kunrei VARCHAR(255) NOT NULL, kana VARCHAR(255) NOT NULL, published TINYINT NOT NULL, jlpt VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, slug VARCHAR(255) DEFAULT NULL, theme_id INT DEFAULT NULL, INDEX IDX_9099C97B3F31F002 (meaning), UNIQUE INDEX UNIQ_9099C97B989D9B62 (slug), INDEX IDX_9099C97B59027487 (theme_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE vocabulary ADD CONSTRAINT FK_9099C97B59027487 FOREIGN KEY (theme_id) REFERENCES theme (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vocabulary DROP FOREIGN KEY FK_9099C97B59027487');
        $this->addSql('DROP TABLE vocabulary');
    }
}
