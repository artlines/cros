<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181225064646 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA participating');
        $this->addSql('CREATE SCHEMA abode');
        $this->addSql('CREATE SCHEMA content');
        $this->addSql('CREATE TABLE participating.conference_member (id SERIAL NOT NULL, user_id INT NOT NULL, conference_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F39DB075A76ED395 ON participating.conference_member (user_id)');
        $this->addSql('CREATE INDEX IDX_F39DB075604B8382 ON participating.conference_member (conference_id)');
        $this->addSql('CREATE TABLE abode.participation_class (id SERIAL NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE sponsor_type (id SERIAL NOT NULL, name_type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE hall (id SERIAL NOT NULL, hall_name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE organization_status (id SERIAL NOT NULL, title VARCHAR(255) NOT NULL, priority INT DEFAULT 0 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE organization_type (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, organization_id INT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, middle_name VARCHAR(255) DEFAULT NULL, post VARCHAR(255) DEFAULT NULL, phone BIGINT NOT NULL, email VARCHAR(255) NOT NULL, is_active BOOLEAN NOT NULL, password VARCHAR(255) NOT NULL, telegram VARCHAR(255) DEFAULT NULL, roles VARCHAR(255) NOT NULL, nickname VARCHAR(255) DEFAULT NULL, car_number VARCHAR(255) DEFAULT NULL, saved BOOLEAN DEFAULT NULL, regdate TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, female BOOLEAN DEFAULT NULL, arrival TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'2018-05-16 14:00\' NOT NULL, departures TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'2018-05-19 14:00\' NOT NULL, leaving TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'2018-05-19 12:00\' NOT NULL, tm_add TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649444F97DD ON "user" (phone)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64943320DA ON "user" (telegram)');
        $this->addSql('CREATE INDEX IDX_8D93D64932C8A3DE ON "user" (organization_id)');
        $this->addSql('CREATE TABLE users_conferences (user_id INT NOT NULL, conference_id INT NOT NULL, PRIMARY KEY(user_id, conference_id))');
        $this->addSql('CREATE INDEX IDX_7995AC48A76ED395 ON users_conferences (user_id)');
        $this->addSql('CREATE INDEX IDX_7995AC48604B8382 ON users_conferences (conference_id)');
        $this->addSql('CREATE TABLE sponsor (id SERIAL NOT NULL, type INT DEFAULT NULL, name VARCHAR(255) NOT NULL, phone BIGINT NOT NULL, url VARCHAR(255) NOT NULL, logo VARCHAR(255) NOT NULL, logo_resize VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, active BOOLEAN DEFAULT NULL, priority BIGINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_818CC9D48CDE5729 ON sponsor (type)');
        $this->addSql('CREATE TABLE abode.room (id SERIAL NOT NULL, type_id INT NOT NULL, apartment_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AC1BF5F0C54C8C93 ON abode.room (type_id)');
        $this->addSql('CREATE INDEX IDX_AC1BF5F0176DFE85 ON abode.room (apartment_id)');
        $this->addSql('CREATE TABLE abode.apartment (id SERIAL NOT NULL, housing_id INT NOT NULL, type_id INT NOT NULL, "number" INT NOT NULL, floor_number INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9812FB9728EFCEA7 ON abode.apartment ("number")');
        $this->addSql('CREATE INDEX IDX_9812FB97AD5873E3 ON abode.apartment (housing_id)');
        $this->addSql('CREATE INDEX IDX_9812FB97C54C8C93 ON abode.apartment (type_id)');
        $this->addSql('CREATE TABLE abode.room_type (id SERIAL NOT NULL, participation_class_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, max_places INT NOT NULL, cost INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3AB62E8E6288787 ON abode.room_type (participation_class_id)');
        $this->addSql('CREATE TABLE abode.housing (id SERIAL NOT NULL, num_of_floors INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE abode.apartment_type (id SERIAL NOT NULL, title VARCHAR(255) NOT NULL, max_rooms INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE abode.place (id SERIAL NOT NULL, conference_member_id INT DEFAULT NULL, room_id INT NOT NULL, approved BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AEA36FB9393B8266 ON abode.place (conference_member_id)');
        $this->addSql('CREATE INDEX IDX_AEA36FB954177093 ON abode.place (room_id)');
        $this->addSql('CREATE TABLE content.faq (id SERIAL NOT NULL, question TEXT NOT NULL, answer TEXT NOT NULL, is_active BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE content.info (id SERIAL NOT NULL, conference_id INT DEFAULT NULL, alias VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, content TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C25915ED604B8382 ON content.info (conference_id)');
        $this->addSql('CREATE TABLE conference (id SERIAL NOT NULL, year INT NOT NULL, registration_start TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, registration_finish TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, event_start TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, event_finish TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE interview (id SERIAL NOT NULL, company INT DEFAULT NULL, name VARCHAR(255) NOT NULL, visits INT NOT NULL, qualityOrganization INT NOT NULL, qualityOrganizationComents TEXT DEFAULT NULL, presentations INT NOT NULL, PresentationsComents TEXT DEFAULT NULL, tables INT NOT NULL, tablesComents TEXT DEFAULT NULL, entertainment INT NOT NULL, entertainmentComents TEXT DEFAULT NULL, food VARCHAR(255) NOT NULL, foodComents TEXT DEFAULT NULL, search INT NOT NULL, searchComents TEXT DEFAULT NULL, informationalResources VARCHAR(255) NOT NULL, informationalResourcesComents TEXT DEFAULT NULL, whatImportant VARCHAR(2048) DEFAULT NULL, whatImportantComent TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CF1D3C344FBF094F ON interview (company)');
        $this->addSql('CREATE TABLE lecture (id SERIAL NOT NULL, date DATE NOT NULL, start_time TIME(0) WITHOUT TIME ZONE NOT NULL, end_time TIME(0) WITHOUT TIME ZONE NOT NULL, hall VARCHAR(50) NOT NULL, hall_id INT DEFAULT NULL, speaker VARCHAR(255) NOT NULL, company VARCHAR(255) NOT NULL, moderator VARCHAR(50) NOT NULL, title VARCHAR(255) NOT NULL, theses TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE speaker (id SERIAL NOT NULL, user_id INT NOT NULL, avatar TEXT DEFAULT NULL, avatar_big TEXT DEFAULT NULL, avatar_small TEXT DEFAULT NULL, image TEXT DEFAULT NULL, description TEXT DEFAULT NULL, publish BOOLEAN DEFAULT NULL, conference_id INT NOT NULL, report VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7B85DB61A76ED395 ON speaker (user_id)');
        $this->addSql('CREATE TABLE append_text (id SERIAL NOT NULL, alias VARCHAR(255) NOT NULL, text TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8C5C4AB3E16C6B94 ON append_text (alias)');
        $this->addSql('CREATE TABLE organization (id SERIAL NOT NULL, status INT NOT NULL, name VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, requisites TEXT DEFAULT NULL, address TEXT DEFAULT NULL, is_active BOOLEAN NOT NULL, manager INT DEFAULT NULL, inn VARCHAR(255) NOT NULL, kpp VARCHAR(255) DEFAULT NULL, sponsor BOOLEAN NOT NULL, regdate TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, comment TEXT DEFAULT NULL, our_comment TEXT DEFAULT NULL, hidden BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C1EE637C7B00651C ON organization (status)');
        $this->addSql('CREATE TABLE organizations_conferences (organization_id INT NOT NULL, conference_id INT NOT NULL, PRIMARY KEY(organization_id, conference_id))');
        $this->addSql('CREATE INDEX IDX_3EBE245A32C8A3DE ON organizations_conferences (organization_id)');
        $this->addSql('CREATE INDEX IDX_3EBE245A604B8382 ON organizations_conferences (conference_id)');
        $this->addSql('CREATE TABLE tgchat (id SERIAL NOT NULL, chat_id INT NOT NULL, is_active BOOLEAN NOT NULL, allow_notify BOOLEAN DEFAULT \'true\' NOT NULL, joined TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, state TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4EBA689F1A9A7125 ON tgchat (chat_id)');
        $this->addSql('CREATE TABLE tgchat_lecture (tgchat_id INT NOT NULL, lecture_id INT NOT NULL, PRIMARY KEY(tgchat_id, lecture_id))');
        $this->addSql('CREATE INDEX IDX_A744EAD1169E818E ON tgchat_lecture (tgchat_id)');
        $this->addSql('CREATE INDEX IDX_A744EAD135E32FCD ON tgchat_lecture (lecture_id)');
        $this->addSql('CREATE TABLE speaker_reports (id SERIAL NOT NULL, report VARCHAR(255) NOT NULL, speaker_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE participating.conference_member ADD CONSTRAINT FK_F39DB075A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE participating.conference_member ADD CONSTRAINT FK_F39DB075604B8382 FOREIGN KEY (conference_id) REFERENCES conference (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D64932C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users_conferences ADD CONSTRAINT FK_7995AC48A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users_conferences ADD CONSTRAINT FK_7995AC48604B8382 FOREIGN KEY (conference_id) REFERENCES conference (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sponsor ADD CONSTRAINT FK_818CC9D48CDE5729 FOREIGN KEY (type) REFERENCES sponsor_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE abode.room ADD CONSTRAINT FK_AC1BF5F0C54C8C93 FOREIGN KEY (type_id) REFERENCES abode.room_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE abode.room ADD CONSTRAINT FK_AC1BF5F0176DFE85 FOREIGN KEY (apartment_id) REFERENCES abode.apartment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE abode.apartment ADD CONSTRAINT FK_9812FB97AD5873E3 FOREIGN KEY (housing_id) REFERENCES abode.housing (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE abode.apartment ADD CONSTRAINT FK_9812FB97C54C8C93 FOREIGN KEY (type_id) REFERENCES abode.apartment_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE abode.room_type ADD CONSTRAINT FK_3AB62E8E6288787 FOREIGN KEY (participation_class_id) REFERENCES abode.participation_class (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE abode.place ADD CONSTRAINT FK_AEA36FB9393B8266 FOREIGN KEY (conference_member_id) REFERENCES participating.conference_member (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE abode.place ADD CONSTRAINT FK_AEA36FB954177093 FOREIGN KEY (room_id) REFERENCES abode.room (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content.info ADD CONSTRAINT FK_C25915ED604B8382 FOREIGN KEY (conference_id) REFERENCES conference (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE interview ADD CONSTRAINT FK_CF1D3C344FBF094F FOREIGN KEY (company) REFERENCES organization (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE speaker ADD CONSTRAINT FK_7B85DB61A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE organization ADD CONSTRAINT FK_C1EE637C7B00651C FOREIGN KEY (status) REFERENCES organization_status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE organizations_conferences ADD CONSTRAINT FK_3EBE245A32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE organizations_conferences ADD CONSTRAINT FK_3EBE245A604B8382 FOREIGN KEY (conference_id) REFERENCES conference (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tgchat_lecture ADD CONSTRAINT FK_A744EAD1169E818E FOREIGN KEY (tgchat_id) REFERENCES tgchat (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tgchat_lecture ADD CONSTRAINT FK_A744EAD135E32FCD FOREIGN KEY (lecture_id) REFERENCES lecture (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE abode.place DROP CONSTRAINT FK_AEA36FB9393B8266');
        $this->addSql('ALTER TABLE abode.room_type DROP CONSTRAINT FK_3AB62E8E6288787');
        $this->addSql('ALTER TABLE sponsor DROP CONSTRAINT FK_818CC9D48CDE5729');
        $this->addSql('ALTER TABLE organization DROP CONSTRAINT FK_C1EE637C7B00651C');
        $this->addSql('ALTER TABLE participating.conference_member DROP CONSTRAINT FK_F39DB075A76ED395');
        $this->addSql('ALTER TABLE users_conferences DROP CONSTRAINT FK_7995AC48A76ED395');
        $this->addSql('ALTER TABLE speaker DROP CONSTRAINT FK_7B85DB61A76ED395');
        $this->addSql('ALTER TABLE abode.place DROP CONSTRAINT FK_AEA36FB954177093');
        $this->addSql('ALTER TABLE abode.room DROP CONSTRAINT FK_AC1BF5F0176DFE85');
        $this->addSql('ALTER TABLE abode.room DROP CONSTRAINT FK_AC1BF5F0C54C8C93');
        $this->addSql('ALTER TABLE abode.apartment DROP CONSTRAINT FK_9812FB97AD5873E3');
        $this->addSql('ALTER TABLE abode.apartment DROP CONSTRAINT FK_9812FB97C54C8C93');
        $this->addSql('ALTER TABLE participating.conference_member DROP CONSTRAINT FK_F39DB075604B8382');
        $this->addSql('ALTER TABLE users_conferences DROP CONSTRAINT FK_7995AC48604B8382');
        $this->addSql('ALTER TABLE content.info DROP CONSTRAINT FK_C25915ED604B8382');
        $this->addSql('ALTER TABLE organizations_conferences DROP CONSTRAINT FK_3EBE245A604B8382');
        $this->addSql('ALTER TABLE tgchat_lecture DROP CONSTRAINT FK_A744EAD135E32FCD');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D64932C8A3DE');
        $this->addSql('ALTER TABLE interview DROP CONSTRAINT FK_CF1D3C344FBF094F');
        $this->addSql('ALTER TABLE organizations_conferences DROP CONSTRAINT FK_3EBE245A32C8A3DE');
        $this->addSql('ALTER TABLE tgchat_lecture DROP CONSTRAINT FK_A744EAD1169E818E');
        $this->addSql('DROP TABLE participating.conference_member');
        $this->addSql('DROP TABLE abode.participation_class');
        $this->addSql('DROP TABLE sponsor_type');
        $this->addSql('DROP TABLE hall');
        $this->addSql('DROP TABLE organization_status');
        $this->addSql('DROP TABLE organization_type');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE users_conferences');
        $this->addSql('DROP TABLE sponsor');
        $this->addSql('DROP TABLE abode.room');
        $this->addSql('DROP TABLE abode.apartment');
        $this->addSql('DROP TABLE abode.room_type');
        $this->addSql('DROP TABLE abode.housing');
        $this->addSql('DROP TABLE abode.apartment_type');
        $this->addSql('DROP TABLE abode.place');
        $this->addSql('DROP TABLE content.faq');
        $this->addSql('DROP TABLE content.info');
        $this->addSql('DROP TABLE conference');
        $this->addSql('DROP TABLE interview');
        $this->addSql('DROP TABLE lecture');
        $this->addSql('DROP TABLE speaker');
        $this->addSql('DROP TABLE append_text');
        $this->addSql('DROP TABLE organization');
        $this->addSql('DROP TABLE organizations_conferences');
        $this->addSql('DROP TABLE tgchat');
        $this->addSql('DROP TABLE tgchat_lecture');
        $this->addSql('DROP TABLE speaker_reports');
    }
}
