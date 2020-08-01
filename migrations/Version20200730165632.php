<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200730165632 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notes (id INT AUTO_INCREMENT NOT NULL, element_id INT NOT NULL, etudiant_id INT NOT NULL, professeur_id INT NOT NULL, note INT DEFAULT NULL, observations LONGTEXT DEFAULT NULL, INDEX IDX_11BA68C1F1F2A24 (element_id), INDEX IDX_11BA68CDDEAB1A3 (etudiant_id), INDEX IDX_11BA68CBAB22EE9 (professeur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notes ADD CONSTRAINT FK_11BA68C1F1F2A24 FOREIGN KEY (element_id) REFERENCES element (id)');
        $this->addSql('ALTER TABLE notes ADD CONSTRAINT FK_11BA68CDDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id)');
        $this->addSql('ALTER TABLE notes ADD CONSTRAINT FK_11BA68CBAB22EE9 FOREIGN KEY (professeur_id) REFERENCES professeur (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE notes');
    }
}
