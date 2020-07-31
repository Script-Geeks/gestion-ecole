<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200730175913 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF96EC013E12');
        $this->addSql('DROP INDEX IDX_8F87BF96EC013E12 ON classe');
        $this->addSql('ALTER TABLE classe DROP emploi_id');
        $this->addSql('ALTER TABLE element DROP FOREIGN KEY FK_41405E39EC013E12');
        $this->addSql('DROP INDEX IDX_41405E39EC013E12 ON element');
        $this->addSql('ALTER TABLE element DROP emploi_id');
        $this->addSql('ALTER TABLE emploi ADD element_id INT NOT NULL, ADD professeur_id INT NOT NULL, ADD classe_id INT NOT NULL');
        $this->addSql('ALTER TABLE emploi ADD CONSTRAINT FK_74A0B0FA1F1F2A24 FOREIGN KEY (element_id) REFERENCES element (id)');
        $this->addSql('ALTER TABLE emploi ADD CONSTRAINT FK_74A0B0FABAB22EE9 FOREIGN KEY (professeur_id) REFERENCES professeur (id)');
        $this->addSql('ALTER TABLE emploi ADD CONSTRAINT FK_74A0B0FA8F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
        $this->addSql('CREATE INDEX IDX_74A0B0FA1F1F2A24 ON emploi (element_id)');
        $this->addSql('CREATE INDEX IDX_74A0B0FABAB22EE9 ON emploi (professeur_id)');
        $this->addSql('CREATE INDEX IDX_74A0B0FA8F5EA509 ON emploi (classe_id)');
        $this->addSql('ALTER TABLE professeur DROP FOREIGN KEY FK_17A55299EC013E12');
        $this->addSql('DROP INDEX IDX_17A55299EC013E12 ON professeur');
        $this->addSql('ALTER TABLE professeur DROP emploi_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE classe ADD emploi_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF96EC013E12 FOREIGN KEY (emploi_id) REFERENCES emploi (id)');
        $this->addSql('CREATE INDEX IDX_8F87BF96EC013E12 ON classe (emploi_id)');
        $this->addSql('ALTER TABLE element ADD emploi_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE element ADD CONSTRAINT FK_41405E39EC013E12 FOREIGN KEY (emploi_id) REFERENCES emploi (id)');
        $this->addSql('CREATE INDEX IDX_41405E39EC013E12 ON element (emploi_id)');
        $this->addSql('ALTER TABLE emploi DROP FOREIGN KEY FK_74A0B0FA1F1F2A24');
        $this->addSql('ALTER TABLE emploi DROP FOREIGN KEY FK_74A0B0FABAB22EE9');
        $this->addSql('ALTER TABLE emploi DROP FOREIGN KEY FK_74A0B0FA8F5EA509');
        $this->addSql('DROP INDEX IDX_74A0B0FA1F1F2A24 ON emploi');
        $this->addSql('DROP INDEX IDX_74A0B0FABAB22EE9 ON emploi');
        $this->addSql('DROP INDEX IDX_74A0B0FA8F5EA509 ON emploi');
        $this->addSql('ALTER TABLE emploi DROP element_id, DROP professeur_id, DROP classe_id');
        $this->addSql('ALTER TABLE professeur ADD emploi_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE professeur ADD CONSTRAINT FK_17A55299EC013E12 FOREIGN KEY (emploi_id) REFERENCES emploi (id)');
        $this->addSql('CREATE INDEX IDX_17A55299EC013E12 ON professeur (emploi_id)');
    }
}
