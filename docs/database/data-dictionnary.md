# Dictionnaire de données

Base `campus_scheduler` (PostgreSQL 16). Le schéma réel est défini dans [`backend/database/init.sql`](../../backend/database/init.sql).

## users — comptes d'authentification

| Colonne | Type | Contraintes | Description |
| --- | --- | --- | --- |
| id | SERIAL | PK | Identifiant |
| email | VARCHAR(255) | NOT NULL, UNIQUE | Adresse email de connexion |
| password | VARCHAR(255) | NOT NULL | Hash bcrypt du mot de passe |
| role | VARCHAR(50) | NOT NULL, DEFAULT 'ROLE_USER' | ROLE_USER ou ROLE_ADMIN |
| created_at | TIMESTAMP | DEFAULT now() | Date de création |
| updated_at | TIMESTAMP | DEFAULT now() | Date de mise à jour |

## level — niveaux académiques

| Colonne | Type | Contraintes | Description |
| --- | --- | --- | --- |
| id | SERIAL | PK | Identifiant |
| code | VARCHAR(10) | NOT NULL, UNIQUE | Code du niveau (L1, L2, L3, M1, M2) |

## program — programmes d'études

| Colonne | Type | Contraintes | Description |
| --- | --- | --- | --- |
| id | SERIAL | PK | Identifiant |
| code | VARCHAR(50) | NOT NULL, UNIQUE | Code du programme (GB, SR, ASI, IA, DS) |
| name | VARCHAR(255) | NOT NULL | Nom du programme |

## academic_group — groupes académiques

| Colonne | Type | Contraintes | Description |
| --- | --- | --- | --- |
| id | SERIAL | PK | Identifiant |
| group_number | INTEGER | NOT NULL | Numéro du groupe (1, 2…) |
| level_id | INTEGER | NOT NULL, FK → level(id) | Niveau du groupe |
| program_id | INTEGER | NOT NULL, FK → program(id) | Programme du groupe |
| — | — | UNIQUE (level_id, program_id, group_number) | Un même groupe ne peut exister qu'une fois |

## room — salles

| Colonne | Type | Contraintes | Description |
| --- | --- | --- | --- |
| id | SERIAL | PK | Identifiant |
| code | VARCHAR(50) | NOT NULL, UNIQUE | Code salle (A101, B201…) |
| name | VARCHAR(255) | NOT NULL | Nom de la salle |
| type | VARCHAR(50) | NOT NULL | CLASSROOM, LABORATORY ou AUDITORIUM |

## subject — matières

| Colonne | Type | Contraintes | Description |
| --- | --- | --- | --- |
| id | SERIAL | PK | Identifiant |
| code | VARCHAR(50) | NOT NULL, UNIQUE | Code matière (ALGO, BDD…) |
| name | VARCHAR(255) | NOT NULL | Nom de la matière |

## teacher — enseignants

| Colonne | Type | Contraintes | Description |
| --- | --- | --- | --- |
| id | SERIAL | PK | Identifiant |
| first_name | VARCHAR(100) | NOT NULL | Prénom |
| last_name | VARCHAR(100) | NULL | Nom |
| email | VARCHAR(255) | NOT NULL, UNIQUE | Email professionnel |
| is_active | BOOLEAN | NOT NULL, DEFAULT TRUE | Enseignant actif ou non |

## time_slot — créneaux horaires

| Colonne | Type | Contraintes | Description |
| --- | --- | --- | --- |
| id | SERIAL | PK | Identifiant |
| day_of_week | VARCHAR(20) | NOT NULL | MONDAY…FRIDAY |
| start_time | TIME | NOT NULL | Heure de début (08:00, 10:00…) |
| end_time | TIME | NOT NULL | Heure de fin (10:00, 12:00…) |

## schedule_week — semaines d'emploi du temps

| Colonne | Type | Contraintes | Description |
| --- | --- | --- | --- |
| id | SERIAL | PK | Identifiant |
| start_date | DATE | NOT NULL, UNIQUE | Date de début de semaine |
| end_date | DATE | NOT NULL | Date de fin de semaine |
| status | VARCHAR(20) | NOT NULL, DEFAULT 'DRAFT' | DRAFT ou PUBLISHED |
| published_at | TIMESTAMP | NULL | Date/heure de publication (action « publier ») |

## course_session — séances de cours

| Colonne | Type | Contraintes | Description |
| --- | --- | --- | --- |
| id | SERIAL | PK | Identifiant |
| status | VARCHAR(20) | NOT NULL, DEFAULT 'DRAFT' | DRAFT ou PUBLISHED |
| delivery_mode | VARCHAR(255) | NOT NULL, DEFAULT 'PRESENTIAL' | PRESENTIAL ou ONLINE (BR-005, BR-006) |
| teacher_id | INTEGER | NOT NULL, FK → teacher(id) | Enseignant (BR-001) |
| subject_id | INTEGER | NOT NULL, FK → subject(id) | Matière enseignée |
| room_id | INTEGER | NULL, FK → room(id) | Salle (conflits BR-002, requis BR-006) |
| time_slot_id | INTEGER | NOT NULL, FK → time_slot(id) | Créneau (BR-002, BR-003) |
| schedule_week_id | INTEGER | NULL, FK → schedule_week(id) | Semaine (BR-004) |

## course_session_academic_group — liaison séances ↔ groupes (n,n)

| Colonne | Type | Contraintes | Description |
| --- | --- | --- | --- |
| course_session_id | INTEGER | PK partielle, FK → course_session(id) ON DELETE CASCADE | Séance |
| academic_group_id | INTEGER | PK partielle, FK → academic_group(id) ON DELETE CASCADE | Groupe (BR-003) |

## messenger_messages — file de messages Symfony Messenger

| Colonne | Type | Contraintes | Description |
| --- | --- | --- | --- |
| id | BIGSERIAL | PK | Identifiant |
| body | TEXT | NOT NULL | Corps du message |
| headers | TEXT | NOT NULL | En-têtes |
| queue_name | VARCHAR(190) | NOT NULL | Nom de la file |
| created_at / available_at / delivered_at | TIMESTAMP | NOT NULL / NOT NULL / NULL | Cycle de vie du message |

## Rappel des règles métier liées aux données

- **BR-001/002/003** : enseignants, salles et groupes ne peuvent pas être affectés deux fois au même créneau dans la même semaine (salle exceptée si ONLINE).
- **BR-004** : une semaine PUBLISHED (et ses séances) devient immuable.
- **BR-005/006** : ONLINE ≈ pas de salle ; PRESENTIAL ≈ salle obligatoire.