flowchart LR

Admin[Administrateur]
RP[Responsable pédagogique]
Public[Utilisateur public]

UC1((Gérer utilisateurs))
UC2((Gérer enseignants))
UC3((Gérer salles))
UC4((Gérer groupes))
UC5((Gérer matières))

UC6((Créer séance))
UC7((Modifier séance))
UC8((Supprimer séance))
UC9((Vérifier conflits))

UC10((Copier EDT semaine))
UC11((Publier EDT))

UC12((Consulter EDT groupe))
UC13((Consulter EDT enseignant))
UC14((Consulter EDT salle))
UC15((Rechercher salles libres))

Admin --> UC1
Admin --> UC2
Admin --> UC3
Admin --> UC4
Admin --> UC5

RP --> UC2
RP --> UC3
RP --> UC4
RP --> UC5

RP --> UC6
RP --> UC7
RP --> UC8
RP --> UC9
RP --> UC10
RP --> UC11
RP --> UC15

Public --> UC12
Public --> UC13
Public --> UC14