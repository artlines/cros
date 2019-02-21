<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190219120042 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE abode.reserved_places (id SERIAL NOT NULL, room_type_id INT NOT NULL, housing_id INT NOT NULL, count INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B86C2C99296E3073 ON abode.reserved_places (room_type_id)');
        $this->addSql('CREATE INDEX IDX_B86C2C99AD5873E3 ON abode.reserved_places (housing_id)');
        $this->addSql('CREATE UNIQUE INDEX unique_reserved_places_idx ON abode.reserved_places (room_type_id, housing_id)');
        $this->addSql('ALTER TABLE abode.reserved_places ADD CONSTRAINT FK_B86C2C99296E3073 FOREIGN KEY (room_type_id) REFERENCES abode.room_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE abode.reserved_places ADD CONSTRAINT FK_B86C2C99AD5873E3 FOREIGN KEY (housing_id) REFERENCES abode.housing (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE abode.reserved_places');
    }
}