<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180102215730 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE users_list (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, user INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_list_item (listid INT NOT NULL, listitemid INT NOT NULL, name VARCHAR(255) NOT NULL, movie INT NOT NULL, INDEX IDX_3CA880168E44C1EF (listid), PRIMARY KEY(listid, listitemid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE users_list_item ADD CONSTRAINT FK_3CA880168E44C1EF FOREIGN KEY (listid) REFERENCES users_list (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users_list_item DROP FOREIGN KEY FK_3CA880168E44C1EF');
        $this->addSql('DROP TABLE users_list');
        $this->addSql('DROP TABLE users_list_item');
    }
}
