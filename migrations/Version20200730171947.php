<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200730171947 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE emploi (id INT AUTO_INCREMENT NOT NULL, heure_debut INT NOT NULL, heure_fin INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jours (id INT AUTO_INCREMENT NOT NULL, emploi_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, INDEX IDX_F0DAEEEDEC013E12 (emploi_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE jours ADD CONSTRAINT FK_F0DAEEEDEC013E12 FOREIGN KEY (emploi_id) REFERENCES emploi (id)');
        $this->addSql('ALTER TABLE classe ADD emploi_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF96EC013E12 FOREIGN KEY (emploi_id) REFERENCES emploi (id)');
        $this->addSql('CREATE INDEX IDX_8F87BF96EC013E12 ON classe (emploi_id)');
        $this->addSql('ALTER TABLE element ADD emploi_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE element ADD CONSTRAINT FK_41405E39EC013E12 FOREIGN KEY (emploi_id) REFERENCES emploi (id)');
        $this->addSql('CREATE INDEX IDX_41405E39EC013E12 ON element (emploi_id)');
        $this->addSql('ALTER TABLE professeur ADD emploi_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE professeur ADD CONSTRAINT FK_17A55299EC013E12 FOREIGN KEY (emploi_id) REFERENCES emploi (id)');
        $this->addSql('CREATE INDEX IDX_17A55299EC013E12 ON professeur (emploi_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF96EC013E12');
        $this->addSql('ALTER TABLE element DROP FOREIGN KEY FK_41405E39EC013E12');
        $this->addSql('ALTER TABLE jours DROP FOREIGN KEY FK_F0DAEEEDEC013E12');
        $this->addSql('ALTER TABLE professeur DROP FOREIGN KEY FK_17A55299EC013E12');
        $this->addSql('DROP TABLE emploi');
        $this->addSql('DROP TABLE jours');
        $this->addSql('DROP INDEX IDX_8F87BF96EC013E12 ON classe');
        $this->addSql('ALTER TABLE classe DROP emploi_id');
        $this->addSql('DROP INDEX IDX_41405E39EC013E12 ON element');
        $this->addSql('ALTER TABLE element DROP emploi_id');
        $this->addSql('DROP INDEX IDX_17A55299EC013E12 ON professeur');
        $this->addSql('ALTER TABLE professeur DROP emploi_id');
    }
}
