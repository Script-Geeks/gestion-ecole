<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200801102049 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        
        $this->addSql('ALTER TABLE emploi ADD CONSTRAINT FK_74A0B0FA180AA129 FOREIGN KEY (filiere_id) REFERENCES filiere (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE emploi ADD CONSTRAINT FK_74A0B0FA1F1F2A24 FOREIGN KEY (element_id) REFERENCES element (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE emploi ADD CONSTRAINT FK_74A0B0FA8F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE emploi ADD CONSTRAINT FK_74A0B0FABAB22EE9 FOREIGN KEY (professeur_id) REFERENCES professeur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notes DROP FOREIGN KEY FK_11BA68C1F1F2A24');
        $this->addSql('ALTER TABLE notes DROP FOREIGN KEY FK_11BA68CBAB22EE9');
        $this->addSql('ALTER TABLE notes DROP FOREIGN KEY FK_11BA68CDDEAB1A3');
        $this->addSql('ALTER TABLE notes ADD CONSTRAINT FK_11BA68C1F1F2A24 FOREIGN KEY (element_id) REFERENCES element (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notes ADD CONSTRAINT FK_11BA68CBAB22EE9 FOREIGN KEY (professeur_id) REFERENCES professeur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notes ADD CONSTRAINT FK_11BA68CDDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
       
        $this->addSql('ALTER TABLE emploi ADD CONSTRAINT FK_74A0B0FA1F1F2A24 FOREIGN KEY (element_id) REFERENCES element (id)');
        $this->addSql('ALTER TABLE emploi ADD CONSTRAINT FK_74A0B0FABAB22EE9 FOREIGN KEY (professeur_id) REFERENCES professeur (id)');
        $this->addSql('ALTER TABLE emploi ADD CONSTRAINT FK_74A0B0FA8F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
        $this->addSql('ALTER TABLE emploi ADD CONSTRAINT FK_74A0B0FA180AA129 FOREIGN KEY (filiere_id) REFERENCES filiere (id)');
        $this->addSql('ALTER TABLE notes DROP FOREIGN KEY FK_11BA68C1F1F2A24');
        $this->addSql('ALTER TABLE notes DROP FOREIGN KEY FK_11BA68CDDEAB1A3');
        $this->addSql('ALTER TABLE notes DROP FOREIGN KEY FK_11BA68CBAB22EE9');
        $this->addSql('ALTER TABLE notes ADD CONSTRAINT FK_11BA68C1F1F2A24 FOREIGN KEY (element_id) REFERENCES element (id)');
        $this->addSql('ALTER TABLE notes ADD CONSTRAINT FK_11BA68CDDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id)');
        $this->addSql('ALTER TABLE notes ADD CONSTRAINT FK_11BA68CBAB22EE9 FOREIGN KEY (professeur_id) REFERENCES professeur (id)');
    }
}
