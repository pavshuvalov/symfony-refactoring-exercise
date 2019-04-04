<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180709070239 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE todos (id integer primary key auto_increment, text varchar(255) not null, completed integer(1) not null)');
        $this->addSql('INSERT INTO todos (text, completed) VALUES ("Pull up the code from github", 1)');
        $this->addSql('INSERT INTO todos (text, completed) VALUES ("Refactor the code", 0)');
        $this->addSql('INSERT INTO todos (text, completed) VALUES ("Push into its own repository", 0)');
        $this->addSql('INSERT INTO todos (text, completed) VALUES ("Share the result", 0)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE todos');
    }
}
