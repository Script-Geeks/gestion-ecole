<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200726083844 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certificats DROP FOREIGN KEY FK_D5486F1BDDEAB1A3');
        $this->addSql('ALTER TABLE certificats ADD CONSTRAINT FK_D5486F1BDDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certificats DROP FOREIGN KEY FK_D5486F1BDDEAB1A3');
        $this->addSql('ALTER TABLE certificats ADD CONSTRAINT FK_D5486F1BDDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id)');
    }
}
