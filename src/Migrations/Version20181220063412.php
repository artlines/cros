<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181220063412 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE logs');
        $this->addSql('ALTER TABLE user CHANGE arrival arrival DATETIME DEFAULT \'2018-05-16 14:00\' NOT NULL, CHANGE leaving leaving DATETIME DEFAULT \'2018-05-19 12:00\' NOT NULL, CHANGE departures departures DATETIME DEFAULT \'2018-05-19 14:00\' NOT NULL');
        $this->addSql('ALTER TABLE interview CHANGE company company INT DEFAULT NULL, CHANGE informationalResources informationalResources VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE interview ADD CONSTRAINT FK_CF1D3C344FBF094F FOREIGN KEY (company) REFERENCES organization (id)');
        $this->addSql('CREATE INDEX IDX_CF1D3C344FBF094F ON interview (company)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE logs (id INT AUTO_INCREMENT NOT NULL, entity VARCHAR(255) NOT NULL COLLATE utf8_general_ci, event LONGTEXT NOT NULL COLLATE utf8_general_ci, date DATETIME NOT NULL, element_id INT DEFAULT NULL, readed TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE interview DROP FOREIGN KEY FK_CF1D3C344FBF094F');
        $this->addSql('DROP INDEX IDX_CF1D3C344FBF094F ON interview');
        $this->addSql('ALTER TABLE interview CHANGE company company INT NOT NULL, CHANGE informationalResources informationalResources VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE user CHANGE arrival arrival DATETIME DEFAULT \'2018-05-16 14:00:00\' NOT NULL, CHANGE departures departures DATETIME DEFAULT \'2018-05-19 14:00:00\' NOT NULL, CHANGE leaving leaving DATETIME DEFAULT \'2018-05-19 12:00:00\' NOT NULL');
    }
}
