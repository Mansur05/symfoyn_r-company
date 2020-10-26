<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201026113016 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE holiday (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, time DATETIME NOT NULL)');
        $this->addSql('CREATE TABLE market (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, work_time CLOB NOT NULL, timezone VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE market_holiday (market_id INTEGER NOT NULL, holiday_id INTEGER NOT NULL, PRIMARY KEY(market_id, holiday_id))');
        $this->addSql('CREATE INDEX IDX_D50C5AB4622F3F37 ON market_holiday (market_id)');
        $this->addSql('CREATE INDEX IDX_D50C5AB4830A3EC0 ON market_holiday (holiday_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE holiday');
        $this->addSql('DROP TABLE market');
        $this->addSql('DROP TABLE market_holiday');
    }
}
