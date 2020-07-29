<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200725150001 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certificats DROP FOREIGN KEY FK_D5486F1BA873A5C6');
        $this->addSql('DROP INDEX IDX_D5486F1BA873A5C6 ON certificats');
        $this->addSql('ALTER TABLE certificats DROP etudiants_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certificats ADD etudiants_id INT NOT NULL');
        $this->addSql('ALTER TABLE certificats ADD CONSTRAINT FK_D5486F1BA873A5C6 FOREIGN KEY (etudiants_id) REFERENCES etudiant (id)');
        $this->addSql('CREATE INDEX IDX_D5486F1BA873A5C6 ON certificats (etudiants_id)');
    }
}
