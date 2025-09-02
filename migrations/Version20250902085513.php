<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250902085513 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task ADD parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25727ACA70 FOREIGN KEY (parent_id) REFERENCES task (id)');
        $this->addSql('CREATE INDEX IDX_527EDB25727ACA70 ON task (parent_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25727ACA70');
        $this->addSql('DROP INDEX IDX_527EDB25727ACA70 ON task');
        $this->addSql('ALTER TABLE task DROP parent_id');
    }
}
