<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200623015333 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD responsable_id INT DEFAULT NULL, ADD professeur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64953C59D72 FOREIGN KEY (responsable_id) REFERENCES responsable (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649BAB22EE9 FOREIGN KEY (professeur_id) REFERENCES professeur (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64953C59D72 ON user (responsable_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649BAB22EE9 ON user (professeur_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64953C59D72');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649BAB22EE9');
        $this->addSql('DROP INDEX UNIQ_8D93D64953C59D72 ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D649BAB22EE9 ON user');
        $this->addSql('ALTER TABLE user DROP responsable_id, DROP professeur_id');
    }
}
