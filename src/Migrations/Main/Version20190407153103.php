<?php

declare(strict_types=1);

namespace DoctrineMigrations\Main;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190407153103 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE IF NOT EXISTS `todos` (`id` integer primary key auto_increment, `text` varchar(255) not null, `completed` tinyint not null)');
        $this->addSql('create index `GET_completed` on `todos` (`completed` ASC)');
        $this->addSql('INSERT INTO `todos` (`text`, `completed`) VALUES ("Pull up the code from github", 1)');
        $this->addSql('INSERT INTO `todos` (`text`, `completed`) VALUES ("Refactor the code", 0)');
        $this->addSql('INSERT INTO `todos` (`text`, `completed`) VALUES ("Push into its own repository", 0)');
        $this->addSql('INSERT INTO `todos` (`text`, `completed`) VALUES ("Share the result", 0)');
    }

    public function down(Schema $schema) : void {}
}
