sequenceDiagram

actor User

participant UI as React Frontend

participant API as Symfony API

participant DB as PostgreSQL

User->>UI: Sélectionner groupe

UI->>API: GET /timetables/group/{id}

API->>DB: Rechercher EDT publié

DB-->>API: Résultat

API-->>UI: JSON

UI-->>User: Affichage emploi du temps