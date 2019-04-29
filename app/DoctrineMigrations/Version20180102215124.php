<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180102215124 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users_list_item ADD CONSTRAINT FK_3CA880168E44C1EF FOREIGN KEY (listid) REFERENCES users_list (id)');
        $this->addSql('CREATE INDEX IDX_3CA880168E44C1EF ON users_list_item (listid)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users_list_item DROP FOREIGN KEY FK_3CA880168E44C1EF');
        $this->addSql('DROP INDEX IDX_3CA880168E44C1EF ON users_list_item');
    }
}
