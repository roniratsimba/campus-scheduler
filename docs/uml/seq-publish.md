sequenceDiagram

actor RP as Responsable Pédagogique

participant UI as React Frontend

participant API as Symfony API

participant WS as ScheduleWeekService

participant DB as PostgreSQL

RP->>UI: Publier semaine

UI->>API: POST /schedule-weeks/{id}/publish

API->>WS: publishWeek()

WS->>DB: Vérifier existence conflits

DB-->>WS: Aucun conflit

WS->>DB: UPDATE status=PUBLISHED

DB-->>WS: OK

WS-->>API: Publication réussie

API-->>UI: 200 OK

UI-->>RP: EDT publié