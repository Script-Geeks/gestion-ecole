<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200728142618 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE module_filiere');
        $this->addSql('ALTER TABLE module ADD filiere_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE module ADD CONSTRAINT FK_C242628180AA129 FOREIGN KEY (filiere_id) REFERENCES filiere (id)');
        $this->addSql('CREATE INDEX IDX_C242628180AA129 ON module (filiere_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE module_filiere (module_id INT NOT NULL, filiere_id INT NOT NULL, INDEX IDX_85AB6387AFC2B591 (module_id), INDEX IDX_85AB6387180AA129 (filiere_id), PRIMARY KEY(module_id, filiere_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE module_filiere ADD CONSTRAINT FK_85AB6387180AA129 FOREIGN KEY (filiere_id) REFERENCES filiere (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE module_filiere ADD CONSTRAINT FK_85AB6387AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE module DROP FOREIGN KEY FK_C242628180AA129');
        $this->addSql('DROP INDEX IDX_C242628180AA129 ON module');
        $this->addSql('ALTER TABLE module DROP filiere_id');
    }
}
