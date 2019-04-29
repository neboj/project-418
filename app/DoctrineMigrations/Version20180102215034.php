<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180102215034 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users_list_item DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE users_list_item CHANGE listid list_id INT NOT NULL');
        $this->addSql('ALTER TABLE users_list_item ADD CONSTRAINT FK_3CA880163DAE168B FOREIGN KEY (list_id) REFERENCES users_list (id)');
        $this->addSql('CREATE INDEX IDX_3CA880163DAE168B ON users_list_item (list_id)');
        $this->addSql('ALTER TABLE users_list_item ADD PRIMARY KEY (list_id, listitemid)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users_list_item DROP FOREIGN KEY FK_3CA880163DAE168B');
        $this->addSql('DROP INDEX IDX_3CA880163DAE168B ON users_list_item');
        $this->addSql('ALTER TABLE users_list_item DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE users_list_item CHANGE list_id listid INT NOT NULL');
        $this->addSql('ALTER TABLE users_list_item ADD PRIMARY KEY (listid, listitemid)');
    }
}
