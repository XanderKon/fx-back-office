<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230530174213 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rate (id SERIAL NOT NULL, source_id INT DEFAULT NULL, base VARCHAR(255) NOT NULL, target VARCHAR(255) NOT NULL, rate DOUBLE PRECISION NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DFEC3F39953C1C61 ON rate (source_id)');
        $this->addSql('COMMENT ON COLUMN rate.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN rate.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE source (id SERIAL NOT NULL, title VARCHAR(255) NOT NULL, is_active BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN source.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE rate ADD CONSTRAINT FK_DFEC3F39953C1C61 FOREIGN KEY (source_id) REFERENCES source (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Just for complete flow
        $this->addSql('INSERT INTO source (title, is_active, created_at) VALUES (\'ecb\', true, NOW())');
        $this->addSql('INSERT INTO source (title, is_active, created_at) VALUES (\'coindesk\', true, NOW())');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rate DROP CONSTRAINT FK_DFEC3F39953C1C61');
        $this->addSql('DROP TABLE rate');
        $this->addSql('DROP TABLE source');
    }
}
