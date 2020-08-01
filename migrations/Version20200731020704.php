<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200731020704 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE emploi ADD niveau_id INT NOT NULL');
        $this->addSql('ALTER TABLE emploi ADD CONSTRAINT FK_74A0B0FA180AA129 FOREIGN KEY (filiere_id) REFERENCES filiere (id)');
        $this->addSql('ALTER TABLE emploi ADD CONSTRAINT FK_74A0B0FAB3E9C81 FOREIGN KEY (niveau_id) REFERENCES niveau (id)');
        $this->addSql('CREATE INDEX IDX_74A0B0FA180AA129 ON emploi (filiere_id)');
        $this->addSql('CREATE INDEX IDX_74A0B0FAB3E9C81 ON emploi (niveau_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE emploi DROP FOREIGN KEY FK_74A0B0FA180AA129');
        $this->addSql('ALTER TABLE emploi DROP FOREIGN KEY FK_74A0B0FAB3E9C81');
        $this->addSql('DROP INDEX IDX_74A0B0FA180AA129 ON emploi');
        $this->addSql('DROP INDEX IDX_74A0B0FAB3E9C81 ON emploi');
        $this->addSql('ALTER TABLE emploi DROP niveau_id');
    }
}
