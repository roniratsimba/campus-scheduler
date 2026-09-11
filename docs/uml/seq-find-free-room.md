sequenceDiagram

actor Visitor as Visiteur

participant UI as React Frontend

participant API as Symfony API

participant SVC as PublicScheduleService

participant DB as PostgreSQL

Visitor->>UI: Consulter salles libres (jour, créneau, semaine)

UI->>API: GET /public/rooms/free?dayOfWeek=MONDAY&startTime=08:00&endTime=10:00&weekId=1

API->>SVC: findFreeRooms(dayOfWeek, startTime, endTime, weekId)

note over SVC: Valider le format des heures (H:i)

SVC->>DB: Récupérer toutes les salles

DB-->>SVC: Liste des salles

SVC->>DB: Récupérer séances publiées de la semaine (créneau + salle)

DB-->>SVC: Séances (hors séances ONLINE)

SVC->>SVC: Pour chaque salle : filtrer les séances en conflit

note over SVC: Conflit si slotStart < endTime ET slotEnd > startTime (chevauchement)

SVC-->>API: Salles restantes (déjà ordonnées, index réindexés)

API-->>UI: 200 OK (tableau de salles)

UI-->>Visitor: Liste des salles libres