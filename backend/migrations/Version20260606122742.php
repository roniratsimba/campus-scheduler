<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260606122742 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE academic_group (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, group_number INTEGER NOT NULL, level_id INTEGER NOT NULL, program_id INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX IDX_998829005FB14BA7 ON academic_group (level_id)');
        $this->addSql('CREATE INDEX IDX_998829003EB8070A ON academic_group (program_id)');
        $this->addSql('CREATE TABLE course_session (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, status VARCHAR(20) NOT NULL, teacher_id INTEGER NOT NULL, subject_id INTEGER NOT NULL, room_id INTEGER NOT NULL, time_slot_id INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX IDX_D887D03841807E1D ON course_session (teacher_id)');
        $this->addSql('CREATE INDEX IDX_D887D03823EDC87 ON course_session (subject_id)');
        $this->addSql('CREATE INDEX IDX_D887D03854177093 ON course_session (room_id)');
        $this->addSql('CREATE INDEX IDX_D887D038D62B0FA ON course_session (time_slot_id)');
        $this->addSql('CREATE TABLE course_session_academic_group (course_session_id INTEGER NOT NULL, academic_group_id INTEGER NOT NULL, PRIMARY KEY (course_session_id, academic_group_id))');
        $this->addSql('CREATE INDEX IDX_650E5DFBBEDDA25C ON course_session_academic_group (course_session_id)');
        $this->addSql('CREATE INDEX IDX_650E5DFBC1645150 ON course_session_academic_group (academic_group_id)');
        $this->addSql('CREATE TABLE level (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, code VARCHAR(10) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9AEACC1377153098 ON level (code)');
        $this->addSql('CREATE TABLE program (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, code VARCHAR(50) NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_92ED778477153098 ON program (code)');
        $this->addSql('CREATE TABLE room (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, code VARCHAR(50) NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_729F519B77153098 ON room (code)');
        $this->addSql('CREATE TABLE subject (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, code VARCHAR(50) NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FBCE3E7A77153098 ON subject (code)');
        $this->addSql('CREATE TABLE teacher (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) DEFAULT NULL, email VARCHAR(255) NOT NULL, is_active BOOLEAN NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B0F6A6D5E7927C74 ON teacher (email)');
        $this->addSql('CREATE TABLE time_slot (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, day_of_week VARCHAR(20) NOT NULL, start_time TIME NOT NULL, end_time TIME NOT NULL)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('DROP TABLE time_slot');
        $this->addSql('DROP TABLE teacher');
        $this->addSql('DROP TABLE subject');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE program');
        $this->addSql('DROP TABLE level');
        $this->addSql('DROP TABLE course_session_academic_group');
        $this->addSql('DROP TABLE course_session');
        $this->addSql('DROP TABLE academic_group');
    }
}
