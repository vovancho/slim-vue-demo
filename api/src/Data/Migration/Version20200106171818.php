<?php

declare(strict_types=1);

namespace Api\Data\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200106171818 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE task_positions (task_id UUID NOT NULL, PRIMARY KEY(task_id))');
        $this->addSql('CREATE TABLE task_tasks (id UUID NOT NULL, user_id UUID NOT NULL, pushed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, type VARCHAR(16) NOT NULL, name VARCHAR(255) NOT NULL, status VARCHAR(16) NOT NULL, process_percent SMALLINT NOT NULL, error_message TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_97FC7EB7A76ED395 ON task_tasks (user_id)');
        $this->addSql('COMMENT ON COLUMN task_tasks.pushed_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE task_positions ADD CONSTRAINT FK_A9B5282D8DB60186 FOREIGN KEY (task_id) REFERENCES task_tasks (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_tasks ADD CONSTRAINT FK_97FC7EB7A76ED395 FOREIGN KEY (user_id) REFERENCES user_users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE task_positions DROP CONSTRAINT FK_A9B5282D8DB60186');
        $this->addSql('DROP TABLE task_positions');
        $this->addSql('DROP TABLE task_tasks');
    }
}
