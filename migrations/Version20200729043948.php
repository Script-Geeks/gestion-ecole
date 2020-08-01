<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200729043948 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE element DROP FOREIGN KEY FK_41405E398F5EA509');
        $this->addSql('ALTER TABLE element DROP FOREIGN KEY FK_41405E39BAB22EE9');
        $this->addSql('ALTER TABLE element CHANGE professeur_id professeur_id INT NOT NULL, CHANGE classe_id classe_id INT NOT NULL');
        $this->addSql('ALTER TABLE element ADD CONSTRAINT FK_41405E398F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE element ADD CONSTRAINT FK_41405E39BAB22EE9 FOREIGN KEY (professeur_id) REFERENCES professeur (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE element DROP FOREIGN KEY FK_41405E39BAB22EE9');
        $this->addSql('ALTER TABLE element DROP FOREIGN KEY FK_41405E398F5EA509');
        $this->addSql('ALTER TABLE element CHANGE professeur_id professeur_id INT DEFAULT NULL, CHANGE classe_id classe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE element ADD CONSTRAINT FK_41405E39BAB22EE9 FOREIGN KEY (professeur_id) REFERENCES professeur (id)');
        $this->addSql('ALTER TABLE element ADD CONSTRAINT FK_41405E398F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
    }
}
