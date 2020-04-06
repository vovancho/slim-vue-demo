<?php

declare(strict_types=1);

namespace App\Data\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200409063558 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE task_handler_tasks (id UUID NOT NULL, pushed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, visibility VARCHAR(16) NOT NULL, name VARCHAR(255) NOT NULL, status VARCHAR(16) NOT NULL, process_percent SMALLINT NOT NULL, author_id UUID NOT NULL, author_email VARCHAR(255) NOT NULL, error_message VARCHAR(255) DEFAULT NULL, error_trace TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN task_handler_tasks.id IS \'(DC2Type:task_handler_task_id)\'');
        $this->addSql('COMMENT ON COLUMN task_handler_tasks.pushed_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN task_handler_tasks.visibility IS \'(DC2Type:task_handler_task_visibility)\'');
        $this->addSql('COMMENT ON COLUMN task_handler_tasks.status IS \'(DC2Type:task_handler_task_status)\'');
        $this->addSql('COMMENT ON COLUMN task_handler_tasks.author_id IS \'(DC2Type:task_handler_author_id)\'');
        $this->addSql('COMMENT ON COLUMN task_handler_tasks.author_email IS \'(DC2Type:task_handler_author_email)\'');
        $this->addSql('CREATE TABLE task_handler_positions (task_id UUID NOT NULL, PRIMARY KEY(task_id))');
        $this->addSql('COMMENT ON COLUMN task_handler_positions.task_id IS \'(DC2Type:task_handler_task_id)\'');
        $this->addSql('CREATE TABLE oauth_access_tokens (identifier VARCHAR(80) NOT NULL, expiry_date_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, user_identifier UUID NOT NULL, client VARCHAR(255) NOT NULL, scopes JSON NOT NULL, PRIMARY KEY(identifier))');
        $this->addSql('COMMENT ON COLUMN oauth_access_tokens.client IS \'(DC2Type:oauth_client)\'');
        $this->addSql('COMMENT ON COLUMN oauth_access_tokens.scopes IS \'(DC2Type:oauth_scopes)\'');
        $this->addSql('CREATE TABLE oauth_auth_codes (identifier VARCHAR(80) NOT NULL, redirect_uri VARCHAR(255) NOT NULL, expiry_date_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, user_identifier UUID NOT NULL, client VARCHAR(255) NOT NULL, scopes JSON NOT NULL, PRIMARY KEY(identifier, redirect_uri))');
        $this->addSql('COMMENT ON COLUMN oauth_auth_codes.client IS \'(DC2Type:oauth_client)\'');
        $this->addSql('COMMENT ON COLUMN oauth_auth_codes.scopes IS \'(DC2Type:oauth_scopes)\'');
        $this->addSql('CREATE TABLE oauth_refresh_tokens (identifier VARCHAR(80) NOT NULL, access_token_identifier VARCHAR(80) NOT NULL, expiry_date_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(identifier))');
        $this->addSql('CREATE INDEX IDX_5AB6878E5675DC ON oauth_refresh_tokens (access_token_identifier)');
        $this->addSql('CREATE TABLE auth_users (id UUID NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, email VARCHAR(255) NOT NULL, password_hash VARCHAR(255) DEFAULT NULL, status VARCHAR(16) NOT NULL, confirm_token_value VARCHAR(255) DEFAULT NULL, confirm_token_expires TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D8A1F49CE7927C74 ON auth_users (email)');
        $this->addSql('COMMENT ON COLUMN auth_users.id IS \'(DC2Type:auth_user_id)\'');
        $this->addSql('COMMENT ON COLUMN auth_users.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN auth_users.email IS \'(DC2Type:auth_user_email)\'');
        $this->addSql('COMMENT ON COLUMN auth_users.status IS \'(DC2Type:auth_user_status)\'');
        $this->addSql('COMMENT ON COLUMN auth_users.confirm_token_expires IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE task_handler_positions ADD CONSTRAINT FK_E2834B5A8DB60186 FOREIGN KEY (task_id) REFERENCES task_handler_tasks (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oauth_refresh_tokens ADD CONSTRAINT FK_5AB6878E5675DC FOREIGN KEY (access_token_identifier) REFERENCES oauth_access_tokens (identifier) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task_handler_positions DROP CONSTRAINT FK_E2834B5A8DB60186');
        $this->addSql('ALTER TABLE oauth_refresh_tokens DROP CONSTRAINT FK_5AB6878E5675DC');
        $this->addSql('DROP TABLE task_handler_tasks');
        $this->addSql('DROP TABLE task_handler_positions');
        $this->addSql('DROP TABLE oauth_access_tokens');
        $this->addSql('DROP TABLE oauth_auth_codes');
        $this->addSql('DROP TABLE oauth_refresh_tokens');
        $this->addSql('DROP TABLE auth_users');
    }
}
