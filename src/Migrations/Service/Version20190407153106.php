<?php

declare(strict_types=1);

namespace DoctrineMigrations\Service;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190407153106 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE IF NOT EXISTS `service`.`antispam_ip` (`ipv4` VARCHAR(15) NOT NULL, `key` VARCHAR(255) NOT NULL, `expire` INT NOT NULL, `count` INT NOT NULL, `is_stat_sent` TINYINT(1) NOT NULL, `extra` JSON NOT NULL, PRIMARY KEY(`ipv4`, `key`))');
    }

    public function down(Schema $schema) : void {}
}
