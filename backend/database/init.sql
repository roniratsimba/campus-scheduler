-- Script d'initialisation de la base de données Campus Scheduler
-- PostgreSQL 16
-- Exécutez ce script avec: psql -U postgres -d campus_scheduler -f init.sql

-- Création de la base de données si elle n'existe pas
-- CREATE DATABASE campus_scheduler;

-- Suppression des tables existantes (pour réinitialisation)
DROP TABLE IF EXISTS course_session_academic_group CASCADE;
DROP TABLE IF EXISTS course_session CASCADE;
DROP TABLE IF EXISTS academic_group CASCADE;
DROP TABLE IF EXISTS time_slot CASCADE;
DROP TABLE IF EXISTS teacher CASCADE;
DROP TABLE IF EXISTS subject CASCADE;
DROP TABLE IF EXISTS room CASCADE;
DROP TABLE IF EXISTS schedule_week CASCADE;
DROP TABLE IF EXISTS program CASCADE;
DROP TABLE IF EXISTS level CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS messenger_messages CASCADE;

-- Création des tables

-- Table users (authentification)
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'ROLE_USER',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table level (niveaux académiques)
CREATE TABLE level (
    id SERIAL PRIMARY KEY,
    code VARCHAR(10) NOT NULL UNIQUE
);

-- Table program (programmes d'études)
CREATE TABLE program (
    id SERIAL PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL
);

-- Table academic_group (groupes académiques)
CREATE TABLE academic_group (
    id SERIAL PRIMARY KEY,
    group_number INTEGER NOT NULL,
    level_id INTEGER NOT NULL,
    program_id INTEGER NOT NULL,
    FOREIGN KEY (level_id) REFERENCES level(id),
    FOREIGN KEY (program_id) REFERENCES program(id)
);

-- Table room (salles)
CREATE TABLE room (
    id SERIAL PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL
);

-- Table subject (matières)
CREATE TABLE subject (
    id SERIAL PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL
);

-- Table teacher (enseignants)
CREATE TABLE teacher (
    id SERIAL PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100),
    email VARCHAR(255) NOT NULL UNIQUE,
    is_active BOOLEAN NOT NULL DEFAULT TRUE
);

-- Table time_slot (créneaux horaires)
CREATE TABLE time_slot (
    id SERIAL PRIMARY KEY,
    day_of_week VARCHAR(20) NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL
);

-- Table schedule_week (semaines d'emploi du temps)
CREATE TABLE schedule_week (
    id SERIAL PRIMARY KEY,
    start_date DATE NOT NULL UNIQUE,
    end_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'DRAFT',
    published_at TIMESTAMP
);

-- Table course_session (séances de cours)
CREATE TABLE course_session (
    id SERIAL PRIMARY KEY,
    status VARCHAR(20) NOT NULL DEFAULT 'DRAFT',
    delivery_mode VARCHAR(255) NOT NULL DEFAULT 'PRESENTIAL',
    teacher_id INTEGER NOT NULL,
    subject_id INTEGER NOT NULL,
    room_id INTEGER,
    time_slot_id INTEGER NOT NULL,
    schedule_week_id INTEGER,
    FOREIGN KEY (teacher_id) REFERENCES teacher(id),
    FOREIGN KEY (subject_id) REFERENCES subject(id),
    FOREIGN KEY (room_id) REFERENCES room(id),
    FOREIGN KEY (time_slot_id) REFERENCES time_slot(id),
    FOREIGN KEY (schedule_week_id) REFERENCES schedule_week(id)
);

-- Table course_session_academic_group (relation many-to-many)
CREATE TABLE course_session_academic_group (
    course_session_id INTEGER NOT NULL,
    academic_group_id INTEGER NOT NULL,
    PRIMARY KEY (course_session_id, academic_group_id),
    FOREIGN KEY (course_session_id) REFERENCES course_session(id) ON DELETE CASCADE,
    FOREIGN KEY (academic_group_id) REFERENCES academic_group(id) ON DELETE CASCADE
);

-- Table messenger_messages (pour Symfony Messenger)
CREATE TABLE messenger_messages (
    id BIGSERIAL PRIMARY KEY,
    body TEXT NOT NULL,
    headers TEXT NOT NULL,
    queue_name VARCHAR(190) NOT NULL,
    created_at TIMESTAMP NOT NULL,
    available_at TIMESTAMP NOT NULL,
    delivered_at TIMESTAMP
);

-- Création des index
CREATE INDEX idx_academic_group_level ON academic_group(level_id);
CREATE INDEX idx_academic_group_program ON academic_group(program_id);
CREATE INDEX idx_course_session_teacher ON course_session(teacher_id);
CREATE INDEX idx_course_session_subject ON course_session(subject_id);
CREATE INDEX idx_course_session_room ON course_session(room_id);
CREATE INDEX idx_course_session_time_slot ON course_session(time_slot_id);
CREATE INDEX idx_course_session_schedule_week ON course_session(schedule_week_id);
CREATE INDEX idx_course_session_group_course ON course_session_academic_group(course_session_id);
CREATE INDEX idx_course_session_group_group ON course_session_academic_group(academic_group_id);
CREATE INDEX idx_messenger_messages ON messenger_messages(queue_name, available_at, delivered_at, id);

-- Insertion des données de démonstration

-- Niveaux académiques
INSERT INTO level (code) VALUES ('L1'), ('L2'), ('L3'), ('M1'), ('M2');

-- Programmes d'études
INSERT INTO program (code, name) VALUES 
    ('GB', 'Génie Logiciel'),
    ('SR', 'Systèmes et Réseaux'),
    ('ASI', 'Administration Systèmes et Infrastructures'),
    ('IA', 'Intelligence Artificielle'),
    ('DS', 'Data Science');

-- Groupes académiques
INSERT INTO academic_group (group_number, level_id, program_id) VALUES 
    (1, 2, 1),  -- L2 GB Groupe 1
    (2, 2, 1),  -- L2 GB Groupe 2
    (1, 3, 2),  -- L3 SR Groupe 1
    (1, 3, 3),  -- L3 ASI Groupe 1
    (1, 4, 4),  -- M1 IA Groupe 1
    (1, 5, 5);  -- M1 DS Groupe 1

-- Enseignants
INSERT INTO teacher (first_name, last_name, email, is_active) VALUES 
    ('Siaka', NULL, 'siaka@campus.local', TRUE),
    ('Jean', 'Rakoto', 'rakoto@campus.local', TRUE),
    ('Marie', 'Dupont', 'marie.dupont@campus.local', TRUE),
    ('Pierre', 'Martin', 'pierre.martin@campus.local', TRUE),
    ('Sophie', 'Bernard', 'sophie.bernard@campus.local', TRUE),
    ('Luc', 'Petit', 'luc.petit@campus.local', TRUE);

-- Matières
INSERT INTO subject (code, name) VALUES 
    ('ALGO', 'Algorithmique'),
    ('BDD', 'Base de données'),
    ('RES', 'Réseaux'),
    ('WEB', 'Développement Web'),
    ('IA', 'Intelligence Artificielle'),
    ('ML', 'Machine Learning'),
    ('SEC', 'Sécurité Informatique'),
    ('DEVOPS', 'DevOps'),
    ('CLOUD', 'Cloud Computing'),
    ('STATS', 'Statistiques');

-- Salles
INSERT INTO room (code, name, type) VALUES 
    ('A101', 'Salle A101', 'CLASSROOM'),
    ('A102', 'Salle A102', 'CLASSROOM'),
    ('A103', 'Salle A103', 'CLASSROOM'),
    ('B201', 'Labo B201', 'LABORATORY'),
    ('B202', 'Labo B202', 'LABORATORY'),
    ('C301', 'Amphithéâtre C301', 'AUDITORIUM'),
    ('C302', 'Amphithéâtre C302', 'AUDITORIUM');

-- Créneaux horaires
INSERT INTO time_slot (day_of_week, start_time, end_time) VALUES 
    ('MONDAY', '08:00:00', '10:00:00'),
    ('MONDAY', '10:00:00', '12:00:00'),
    ('MONDAY', '14:00:00', '16:00:00'),
    ('MONDAY', '16:00:00', '18:00:00'),
    ('TUESDAY', '08:00:00', '10:00:00'),
    ('TUESDAY', '10:00:00', '12:00:00'),
    ('TUESDAY', '14:00:00', '16:00:00'),
    ('TUESDAY', '16:00:00', '18:00:00'),
    ('WEDNESDAY', '08:00:00', '10:00:00'),
    ('WEDNESDAY', '10:00:00', '12:00:00'),
    ('WEDNESDAY', '14:00:00', '16:00:00'),
    ('WEDNESDAY', '16:00:00', '18:00:00'),
    ('THURSDAY', '08:00:00', '10:00:00'),
    ('THURSDAY', '10:00:00', '12:00:00'),
    ('THURSDAY', '14:00:00', '16:00:00'),
    ('THURSDAY', '16:00:00', '18:00:00'),
    ('FRIDAY', '08:00:00', '10:00:00'),
    ('FRIDAY', '10:00:00', '12:00:00'),
    ('FRIDAY', '14:00:00', '16:00:00'),
    ('FRIDAY', '16:00:00', '18:00:00');

-- Semaines d'emploi du temps
INSERT INTO schedule_week (start_date, end_date, status) VALUES 
    ('2026-06-08', '2026-06-13', 'PUBLISHED'),
    ('2026-06-15', '2026-06-20', 'PUBLISHED'),
    ('2026-06-22', '2026-06-27', 'DRAFT'),
    ('2026-06-29', '2026-07-04', 'DRAFT');

-- Séances de cours (données de démonstration)
INSERT INTO course_session (status, delivery_mode, teacher_id, subject_id, room_id, time_slot_id, schedule_week_id) VALUES 
    ('DRAFT', 'PRESENTIAL', 1, 1, 1, 1, 1),  -- Siaka - ALGO - A101 - Lundi 08:00 - Semaine 1
    ('DRAFT', 'PRESENTIAL', 2, 2, 2, 2, 1),  -- Rakoto - BDD - A102 - Lundi 10:00 - Semaine 1
    ('DRAFT', 'PRESENTIAL', 3, 3, 4, 5, 1),  -- Marie - RES - B201 - Mardi 08:00 - Semaine 1
    ('DRAFT', 'PRESENTIAL', 4, 4, 1, 6, 1),  -- Pierre - WEB - A101 - Mardi 10:00 - Semaine 1
    ('DRAFT', 'PRESENTIAL', 5, 5, 5, 9, 1),  -- Sophie - IA - B202 - Mercredi 08:00 - Semaine 1
    ('DRAFT', 'PRESENTIAL', 6, 6, 6, 10, 1), -- Luc - ML - C301 - Mercredi 10:00 - Semaine 1
    ('DRAFT', 'PRESENTIAL', 1, 7, 2, 13, 1), -- Siaka - SEC - A102 - Jeudi 08:00 - Semaine 1
    ('DRAFT', 'PRESENTIAL', 2, 8, 4, 14, 1), -- Rakoto - DEVOPS - B201 - Jeudi 10:00 - Semaine 1
    ('DRAFT', 'PRESENTIAL', 3, 9, 1, 17, 1), -- Marie - CLOUD - A101 - Vendredi 08:00 - Semaine 1
    ('DRAFT', 'PRESENTIAL', 4, 10, 2, 18, 1); -- Pierre - STATS - A102 - Vendredi 10:00 - Semaine 1

-- Association des groupes aux séances
INSERT INTO course_session_academic_group (course_session_id, academic_group_id) VALUES 
    (1, 1),  -- Séance 1 pour groupe L2 GB G1
    (2, 2),  -- Séance 2 pour groupe L2 GB G2
    (3, 3),  -- Séance 3 pour groupe L3 SR G1
    (4, 1),  -- Séance 4 pour groupe L2 GB G1
    (5, 5),  -- Séance 5 pour groupe M1 IA G1
    (6, 6),  -- Séance 6 pour groupe M1 DS G1
    (7, 4),  -- Séance 7 pour groupe L3 ASI G1
    (8, 3),  -- Séance 8 pour groupe L3 SR G1
    (9, 5),  -- Séance 9 pour groupe M1 IA G1
    (10, 6); -- Séance 10 pour groupe M1 DS G1

-- Utilisateur admin pour démonstration (mot de passe: admin123)
-- Le mot de passe est hashé avec bcrypt (password_hash('admin123', PASSWORD_BCRYPT))
INSERT INTO users (email, password, role) VALUES 
    ('admin@campus.local', '$2y$13$8Z1.7Z1.7Z1.7Z1.7Z1.7Z1.7Z1.7Z1.7Z1.7Z1.7Z1.7Z1.7Z1.7', 'ROLE_ADMIN');

-- Affichage du résumé
SELECT 'Base de données initialisée avec succès!' AS message;
SELECT COUNT(*) AS niveaux FROM level;
SELECT COUNT(*) AS programmes FROM program;
SELECT COUNT(*) AS groupes FROM academic_group;
SELECT COUNT(*) AS enseignants FROM teacher;
SELECT COUNT(*) AS matieres FROM subject;
SELECT COUNT(*) AS salles FROM room;
SELECT COUNT(*) AS creneaux FROM time_slot;
SELECT COUNT(*) AS semaines FROM schedule_week;
SELECT COUNT(*) AS seances FROM course_session;
SELECT COUNT(*) AS utilisateurs FROM users;
