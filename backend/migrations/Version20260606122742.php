<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Schéma initial de la base Campus Scheduler (PostgreSQL).
 *
 * Ce fichier génère la même structure que backend/database/init.sql
 * (sans les données de démonstration).
 */
final class Version20260606122742 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création du schéma initial (users, référentiels, semaines, séances)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE users (id SERIAL PRIMARY KEY, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, role VARCHAR(50) NOT NULL DEFAULT \'ROLE_USER\', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');

        $this->addSql('CREATE TABLE level (id SERIAL PRIMARY KEY, code VARCHAR(10) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9AEACC1377153098 ON level (code)');

        $this->addSql('CREATE TABLE program (id SERIAL PRIMARY KEY, code VARCHAR(50) NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_92ED778477153098 ON program (code)');

        $this->addSql('CREATE TABLE academic_group (id SERIAL PRIMARY KEY, group_number INTEGER NOT NULL, level_id INTEGER NOT NULL, program_id INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX IDX_998829005FB14BA7 ON academic_group (level_id)');
        $this->addSql('CREATE INDEX IDX_998829003EB8070A ON academic_group (program_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_99882900_DEDUP ON academic_group (level_id, program_id, group_number)');
        $this->addSql('ALTER TABLE academic_group ADD CONSTRAINT FK_998829005FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE academic_group ADD CONSTRAINT FK_998829003EB8070A FOREIGN KEY (program_id) REFERENCES program (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('CREATE TABLE room (id SERIAL PRIMARY KEY, code VARCHAR(50) NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_729F519B77153098 ON room (code)');

        $this->addSql('CREATE TABLE subject (id SERIAL PRIMARY KEY, code VARCHAR(50) NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FBCE3E7A77153098 ON subject (code)');

        $this->addSql('CREATE TABLE teacher (id SERIAL PRIMARY KEY, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) DEFAULT NULL, email VARCHAR(255) NOT NULL, is_active BOOLEAN NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B0F6A6D5E7927C74 ON teacher (email)');

        $this->addSql('CREATE TABLE time_slot (id SERIAL PRIMARY KEY, day_of_week VARCHAR(20) NOT NULL, start_time TIME NOT NULL, end_time TIME NOT NULL)');

        $this->addSql('CREATE TABLE schedule_week (id SERIAL PRIMARY KEY, start_date DATE NOT NULL, end_date DATE NOT NULL, status VARCHAR(20) NOT NULL DEFAULT \'DRAFT\', published_at TIMESTAMP DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6AEC1F2A_7720E806 ON schedule_week (start_date)');

        $this->addSql('CREATE TABLE course_session (id SERIAL PRIMARY KEY, status VARCHAR(20) NOT NULL DEFAULT \'DRAFT\', delivery_mode VARCHAR(255) NOT NULL DEFAULT \'PRESENTIAL\', teacher_id INTEGER NOT NULL, subject_id INTEGER NOT NULL, room_id INTEGER DEFAULT NULL, time_slot_id INTEGER NOT NULL, schedule_week_id INTEGER DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_D887D03841807E1D ON course_session (teacher_id)');
        $this->addSql('CREATE INDEX IDX_D887D03823EDC87 ON course_session (subject_id)');
        $this->addSql('CREATE INDEX IDX_D887D03854177093 ON course_session (room_id)');
        $this->addSql('CREATE INDEX IDX_D887D038D62B0FA ON course_session (time_slot_id)');
        $this->addSql('CREATE INDEX IDX_D887D038_E9EC5D ON course_session (schedule_week_id)');
        $this->addSql('ALTER TABLE course_session ADD CONSTRAINT FK_D887D03841807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE course_session ADD CONSTRAINT FK_D887D03823EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE course_session ADD CONSTRAINT FK_D887D03854177093 FOREIGN KEY (room_id) REFERENCES room (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE course_session ADD CONSTRAINT FK_D887D038D62B0FA FOREIGN KEY (time_slot_id) REFERENCES time_slot (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE course_session ADD CONSTRAINT FK_D887D038_E9EC5D FOREIGN KEY (schedule_week_id) REFERENCES schedule_week (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('CREATE TABLE course_session_academic_group (course_session_id INTEGER NOT NULL, academic_group_id INTEGER NOT NULL, PRIMARY KEY (course_session_id, academic_group_id))');
        $this->addSql('CREATE INDEX IDX_650E5DFBBEDDA25C ON course_session_academic_group (course_session_id)');
        $this->addSql('CREATE INDEX IDX_650E5DFBC1645150 ON course_session_academic_group (academic_group_id)');
        $this->addSql('ALTER TABLE course_session_academic_group ADD CONSTRAINT FK_650E5DFBBEDDA25C FOREIGN KEY (course_session_id) REFERENCES course_session (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE course_session_academic_group ADD CONSTRAINT FK_650E5DFBC1645150 FOREIGN KEY (academic_group_id) REFERENCES academic_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL PRIMARY KEY, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP NOT NULL, available_at TIMESTAMP NOT NULL, delivered_at TIMESTAMP DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('DROP TABLE course_session_academic_group');
        $this->addSql('DROP TABLE course_session');
        $this->addSql('DROP TABLE schedule_week');
        $this->addSql('DROP TABLE time_slot');
        $this->addSql('DROP TABLE teacher');
        $this->addSql('DROP TABLE subject');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE academic_group');
        $this->addSql('DROP TABLE program');
        $this->addSql('DROP TABLE level');
        $this->addSql('DROP TABLE users');
    }
}