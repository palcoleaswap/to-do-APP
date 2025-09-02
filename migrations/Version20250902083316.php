<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250902083316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE group_user (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE group_user_user (group_user_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_37A1C661216E8799 (group_user_id), INDEX IDX_37A1C661A76ED395 (user_id), PRIMARY KEY(group_user_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE group_user_user ADD CONSTRAINT FK_37A1C661216E8799 FOREIGN KEY (group_user_id) REFERENCES group_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_user_user ADD CONSTRAINT FK_37A1C661A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE group_user_user DROP FOREIGN KEY FK_37A1C661216E8799');
        $this->addSql('ALTER TABLE group_user_user DROP FOREIGN KEY FK_37A1C661A76ED395');
        $this->addSql('DROP TABLE group_user');
        $this->addSql('DROP TABLE group_user_user');
    }
}
