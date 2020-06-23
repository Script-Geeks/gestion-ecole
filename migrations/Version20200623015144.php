<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200623015144 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE professeur DROP FOREIGN KEY FK_17A55299A76ED395');
        $this->addSql('DROP INDEX UNIQ_17A55299A76ED395 ON professeur');
        $this->addSql('ALTER TABLE professeur DROP user_id');
        $this->addSql('ALTER TABLE responsable DROP FOREIGN KEY FK_52520D07A76ED395');
        $this->addSql('DROP INDEX UNIQ_52520D07A76ED395 ON responsable');
        $this->addSql('ALTER TABLE responsable DROP user_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE professeur ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE professeur ADD CONSTRAINT FK_17A55299A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_17A55299A76ED395 ON professeur (user_id)');
        $this->addSql('ALTER TABLE responsable ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE responsable ADD CONSTRAINT FK_52520D07A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_52520D07A76ED395 ON responsable (user_id)');
    }
}
