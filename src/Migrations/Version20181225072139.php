<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181225072139 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE "user" ALTER arrival SET DEFAULT \'2018-05-16 14:00\'');
        $this->addSql('ALTER TABLE "user" ALTER departures SET DEFAULT \'2018-05-19 14:00\'');
        $this->addSql('ALTER TABLE "user" ALTER leaving SET DEFAULT \'2018-05-19 12:00\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_911533C8BB827337 ON conference (year)');
        $this->addSql('ALTER TABLE organization DROP manager');
        $this->addSql('ALTER TABLE organization DROP regdate');
        $this->addSql('ALTER TABLE organization DROP comment');
        $this->addSql('ALTER TABLE organization DROP our_comment');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" ALTER arrival SET DEFAULT \'2018-05-16 14:00:00\'');
        $this->addSql('ALTER TABLE "user" ALTER departures SET DEFAULT \'2018-05-19 14:00:00\'');
        $this->addSql('ALTER TABLE "user" ALTER leaving SET DEFAULT \'2018-05-19 12:00:00\'');
        $this->addSql('ALTER TABLE organization ADD manager INT DEFAULT NULL');
        $this->addSql('ALTER TABLE organization ADD regdate TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE organization ADD comment TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE organization ADD our_comment TEXT DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_911533C8BB827337');
    }
}
