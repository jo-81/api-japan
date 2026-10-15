<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261015070009 extends AbstractMigration
{
    public function getDescription(): string
    {
        return "Création de l'entité Kana";
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE kana (id INT AUTO_INCREMENT NOT NULL, romaji VARCHAR(10) NOT NULL, ideogram VARCHAR(10) NOT NULL, kunrei VARCHAR(10) NOT NULL, position INT NOT NULL, type VARCHAR(255) NOT NULL, accent_type VARCHAR(255) DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_45BD19EE284546FD (ideogram), UNIQUE INDEX uniq_kana_romaji_type (romaji, type), UNIQUE INDEX uniq_kana_kunrei_type (kunrei, type), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE kana_combinations (kana_source INT NOT NULL, kana_target INT NOT NULL, INDEX IDX_2EA0C13595627243 (kana_source), INDEX IDX_2EA0C1358C8722CC (kana_target), PRIMARY KEY (kana_source, kana_target)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE kana_combinations ADD CONSTRAINT FK_2EA0C13595627243 FOREIGN KEY (kana_source) REFERENCES kana (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE kana_combinations ADD CONSTRAINT FK_2EA0C1358C8722CC FOREIGN KEY (kana_target) REFERENCES kana (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE kana_combinations DROP FOREIGN KEY FK_2EA0C13595627243');
        $this->addSql('ALTER TABLE kana_combinations DROP FOREIGN KEY FK_2EA0C1358C8722CC');
        $this->addSql('DROP TABLE kana');
        $this->addSql('DROP TABLE kana_combinations');
    }
}
