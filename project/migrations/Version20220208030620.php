<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220208030620 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE todolist_user (todolist_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_C14A04A0AD16642A (todolist_id), INDEX IDX_C14A04A0A76ED395 (user_id), PRIMARY KEY(todolist_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE todolist_user ADD CONSTRAINT FK_C14A04A0AD16642A FOREIGN KEY (todolist_id) REFERENCES todolist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE todolist_user ADD CONSTRAINT FK_C14A04A0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE todolist_user');
    }
}
