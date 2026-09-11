# Modèle Logique de Données (MLD)

Base PostgreSQL `campus_scheduler`. Génère à l'identique ce que fait `backend/database/init.sql` (et la migration Doctrine correspondante).

## Notation

- Les clés primaires sont soulignées.
- Les clés étrangères sont en *italique*.
- `ON DELETE CASCADE` est précisé quand applicable.

## Tables

> **users** (<u>id</u>, email, password, role, created_at, updated_at)
- `email` : `UNIQUE`
- `role` : `ROLE_USER` | `ROLE_ADMIN`

> **level** (<u>id</u>, code)
- `code` : `UNIQUE` (ex. L1, L2, M1…)

> **program** (<u>id</u>, code, name)
- `code` : `UNIQUE` (ex. GB, SR, IA…)

> **academic_group** (<u>id</u>, group_number, *level_id*, *program_id*)
- `UNIQUE (level_id, program_id, group_number)` — un groupe est unique pour un niveau + programme + numéro
- `FOREIGN KEY (level_id) → level(id)`
- `FOREIGN KEY (program_id) → program(id)`

> **room** (<u>id</u>, code, name, type)
- `code` : `UNIQUE`
- `type` : CLASSROOM | LABORATORY | AUDITORIUM

> **subject** (<u>id</u>, code, name)
- `code` : `UNIQUE`

> **teacher** (<u>id</u>, first_name, last_name, email, is_active)
- `email` : `UNIQUE`

> **time_slot** (<u>id</u>, day_of_week, start_time, end_time)
- `day_of_week` : MONDAY…FRIDAY — `start_time`, `end_time` de type `TIME`

> **schedule_week** (<u>id</u>, start_date, end_date, status, published_at)
- `start_date` : `UNIQUE`
- `status` : DRAFT | PUBLISHED — `published_at` est renseigné par l'action « publier »

> **course_session** (<u>id</u>, status, delivery_mode, *teacher_id*, *subject_id*, *room_id*, *time_slot_id*, *schedule_week_id*)
- `status` : DRAFT | PUBLISHED
- `delivery_mode` : PRESENTIAL | ONLINE (BR-005, BR-006)
- `room_id` : `NULL` autorisé (séance ONLINE ou non encore planifiée)
- `schedule_week_id` : `NULL` autorisé
- `FOREIGN KEY (teacher_id) → teacher(id)`
- `FOREIGN KEY (subject_id) → subject(id)`
- `FOREIGN KEY (room_id) → room(id)`
- `FOREIGN KEY (time_slot_id) → time_slot(id)`
- `FOREIGN KEY (schedule_week_id) → schedule_week(id)`

> **course_session_academic_group** (<u>course_session_id</u>, <u>academic_group_id</u>)
- Table d'association (relation many-to-many) entre séance et groupe
- `PRIMARY KEY (course_session_id, academic_group_id)`
- `ON DELETE CASCADE` sur les deux clés

## Associations

| Relation | Cardinalité | Explication |
| --- | --- | --- |
| program → academic_group | 1,n | Un programme contient plusieurs groupes |
| level → academic_group | 1,n | Un niveau contient plusieurs groupes |
| teacher → course_session | 1,n | Un enseignant donne plusieurs séances |
| subject → course_session | 1,n | Une matière est enseignée dans plusieurs séances |
| room → course_session | 0,n | Une salle accueille 0 à n séances |
| time_slot → course_session | 1,n | Un créneau est utilisé par plusieurs séances |
| schedule_week → course_session | 0,n | Une semaine contient 0 à n séances |
| course_session ↔ academic_group | n,n | Une séance concerne plusieurs groupes (via table de liaison) |

## Contraintes métier (résumé)

Voir le détail dans [`docs/adr/business-rules.md`](../adr/business-rules.md).

- BR-001 : un enseignant ne peut pas être affecté à deux séances sur le même créneau, même semaine.
- BR-002 : une salle ne peut pas être affectée à deux séances sur le même créneau, même semaine (hors ONLINE).
- BR-003 : un groupe ne peut pas être affecté à deux séances sur le même créneau, même semaine.
- BR-004 : une semaine publiée est immuable (plus de création/modification/suppression de séance, ni copie vers elle).
- BR-005 : une séance ONLINE ne nécessite pas de salle.
- BR-006 : une séance PRESENTIAL exige une salle.