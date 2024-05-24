<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240522135037 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__photo AS SELECT id, name, size, updated_at FROM photo');
        $this->addSql('DROP TABLE photo');
        $this->addSql('CREATE TABLE photo (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, plant_id INTEGER DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, size INTEGER DEFAULT NULL, updated_at DATE DEFAULT NULL --(DC2Type:date_immutable)
        , CONSTRAINT FK_14B784181D935652 FOREIGN KEY (plant_id) REFERENCES plant (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO photo (id, name, size, updated_at) SELECT id, name, size, updated_at FROM __temp__photo');
        $this->addSql('DROP TABLE __temp__photo');
        $this->addSql('CREATE INDEX IDX_14B784181D935652 ON photo (plant_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__photo AS SELECT id, name, size, updated_at FROM photo');
        $this->addSql('DROP TABLE photo');
        $this->addSql('CREATE TABLE photo (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, size INTEGER DEFAULT NULL, updated_at DATE DEFAULT NULL --(DC2Type:date_immutable)
        )');
        $this->addSql('INSERT INTO photo (id, name, size, updated_at) SELECT id, name, size, updated_at FROM __temp__photo');
        $this->addSql('DROP TABLE __temp__photo');
    }
}
