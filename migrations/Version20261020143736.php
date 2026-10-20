<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261020143736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return "Création de l'entité Kanji";
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE kanji (id INT AUTO_INCREMENT NOT NULL, ideogram VARCHAR(10) NOT NULL, position INT DEFAULT NULL, number_stroke INT NOT NULL, published TINYINT NOT NULL, jlpt VARCHAR(255) DEFAULT NULL, meanings JSON NOT NULL, kunyomi JSON DEFAULT NULL, onyomi JSON DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_426F9DDC284546FD (ideogram), UNIQUE INDEX UNIQ_426F9DDC462CE4F5 (position), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE kanji_kanji (kanji_source INT NOT NULL, kanji_target INT NOT NULL, INDEX IDX_9466F04E415D8476 (kanji_source), INDEX IDX_9466F04E58B8D4F9 (kanji_target), PRIMARY KEY (kanji_source, kanji_target)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE kanji_vocabulary (kanji_id INT NOT NULL, vocabulary_id INT NOT NULL, INDEX IDX_2AA0CBC3FB3081B8 (kanji_id), INDEX IDX_2AA0CBC3AD0E05F6 (vocabulary_id), PRIMARY KEY (kanji_id, vocabulary_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE kanji_kanji ADD CONSTRAINT FK_9466F04E415D8476 FOREIGN KEY (kanji_source) REFERENCES kanji (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE kanji_kanji ADD CONSTRAINT FK_9466F04E58B8D4F9 FOREIGN KEY (kanji_target) REFERENCES kanji (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE kanji_vocabulary ADD CONSTRAINT FK_2AA0CBC3FB3081B8 FOREIGN KEY (kanji_id) REFERENCES kanji (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE kanji_vocabulary ADD CONSTRAINT FK_2AA0CBC3AD0E05F6 FOREIGN KEY (vocabulary_id) REFERENCES vocabulary (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE kanji_kanji DROP FOREIGN KEY FK_9466F04E415D8476');
        $this->addSql('ALTER TABLE kanji_kanji DROP FOREIGN KEY FK_9466F04E58B8D4F9');
        $this->addSql('ALTER TABLE kanji_vocabulary DROP FOREIGN KEY FK_2AA0CBC3FB3081B8');
        $this->addSql('ALTER TABLE kanji_vocabulary DROP FOREIGN KEY FK_2AA0CBC3AD0E05F6');
        $this->addSql('DROP TABLE kanji');
        $this->addSql('DROP TABLE kanji_kanji');
        $this->addSql('DROP TABLE kanji_vocabulary');
    }
}
