<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260606174816 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE course_session ADD delivery_mode VARCHAR(255) NOT NULL DEFAULT \'PRESENTIAL\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        // SQLite doesn't support DROP COLUMN directly, need to recreate table
        $this->addSql('CREATE TABLE course_session_temp (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, status VARCHAR(20) NOT NULL, teacher_id INTEGER NOT NULL, subject_id INTEGER NOT NULL, room_id INTEGER NOT NULL, time_slot_id INTEGER NOT NULL, schedule_week_id INTEGER DEFAULT NULL)');
        $this->addSql('INSERT INTO course_session_temp (id, status, teacher_id, subject_id, room_id, time_slot_id, schedule_week_id) SELECT id, status, teacher_id, subject_id, room_id, time_slot_id, schedule_week_id FROM course_session');
        $this->addSql('DROP TABLE course_session');
        $this->addSql('ALTER TABLE course_session_temp RENAME TO course_session');
        $this->addSql('CREATE INDEX IDX_D887D03841807E1D ON course_session (teacher_id)');
        $this->addSql('CREATE INDEX IDX_D887D03823EDC87 ON course_session (subject_id)');
        $this->addSql('CREATE INDEX IDX_D887D03854177093 ON course_session (room_id)');
        $this->addSql('CREATE INDEX IDX_D887D038D62B0FA ON course_session (time_slot_id)');
        $this->addSql('CREATE INDEX IDX_D887D0384505179E ON course_session (schedule_week_id)');
    }
}
