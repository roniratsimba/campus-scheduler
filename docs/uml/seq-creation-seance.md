sequenceDiagram

actor RP as Responsable Pédagogique

participant UI as React Frontend

participant API as Symfony API

participant CS as CourseSessionService

participant DB as PostgreSQL

RP->>UI: Remplir formulaire séance

UI->>API: POST /course-sessions

API->>CS: createSession()

CS->>DB: Vérifier conflit salle

DB-->>CS: OK

CS->>DB: Vérifier conflit enseignant

DB-->>CS: OK

CS->>DB: Vérifier conflit groupe

DB-->>CS: OK

CS->>DB: INSERT course_session

DB-->>CS: Session créée

CS-->>API: Succès

API-->>UI: 201 Created

UI-->>RP: Séance créée