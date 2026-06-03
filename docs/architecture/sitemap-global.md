flowchart TD

Home["Accueil"]

GroupTT["EDT par Groupe"]

TeacherTT["EDT par Enseignant"]

RoomTT["EDT par Salle"]

Login["Connexion"]

Dashboard["Dashboard"]

Teachers["Enseignants"]

Subjects["Matières"]

Rooms["Salles"]

Sessions["Séances"]

Weeks["Semaines"]

Home --> GroupTT
Home --> TeacherTT
Home --> RoomTT

Home --> Login

Login --> Dashboard

Dashboard --> Teachers
Dashboard --> Subjects
Dashboard --> Rooms
Dashboard --> Sessions
Dashboard --> Weeks