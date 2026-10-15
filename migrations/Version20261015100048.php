<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261015100048 extends AbstractMigration
{
    public function getDescription(): string
    {
        return "Suppression des index unique sur romaji et kunrei selon le type";
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_kana_kunrei_type ON kana');
        $this->addSql('DROP INDEX uniq_kana_romaji_type ON kana');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX uniq_kana_kunrei_type ON kana (kunrei, type)');
        $this->addSql('CREATE UNIQUE INDEX uniq_kana_romaji_type ON kana (romaji, type)');
    }
}
