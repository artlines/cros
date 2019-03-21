<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190321113036 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA program');
        $this->addSql('CREATE TABLE program.program_member (id SERIAL NOT NULL, conference_member_id INT NOT NULL, type VARCHAR(255) NOT NULL, photo_original TEXT DEFAULT NULL, photo_big TEXT DEFAULT NULL, photo_small TEXT DEFAULT NULL, description TEXT DEFAULT NULL, ordering INT DEFAULT 100 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_65729700393B8266 ON program.program_member (conference_member_id)');
        $this->addSql('ALTER TABLE program.program_member ADD CONSTRAINT FK_65729700393B8266 FOREIGN KEY (conference_member_id) REFERENCES participating.conference_member (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE program.program_member');
    }
}
