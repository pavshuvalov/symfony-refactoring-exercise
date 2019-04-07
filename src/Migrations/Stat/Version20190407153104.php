<?php

declare(strict_types=1);

namespace DoctrineMigrations\Stat;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190407153104 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE IF NOT EXISTS `metric` (`key` VARCHAR(255) NOT NULL, `value` INT NOT NULL, `extra` JSON NOT NULL, PRIMARY KEY(`key`))');
    }

    public function down(Schema $schema) : void {}
}
