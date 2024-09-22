<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240909174604 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client CHANGE prenom prenom VARCHAR(20) NOT NULL, CHANGE cin cin INT NOT NULL');
        $this->addSql('ALTER TABLE reclamation CHANGE clientnom clientnom VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client CHANGE prenom prenom VARCHAR(50) NOT NULL, CHANGE cin cin SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE reclamation CHANGE clientnom clientnom VARCHAR(50) NOT NULL');
    }
}
