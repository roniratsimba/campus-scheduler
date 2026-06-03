flowchart TD

A[Début]

--> B[Choisir semaine]

--> C[Choisir groupe(s)]

--> D[Choisir matière]

--> E[Choisir enseignant]

--> F[Choisir salle]

--> G[Choisir créneau]

--> H{Conflit ?}

H -->|Oui| I[Afficher erreur]

I --> G

H -->|Non| J[Enregistrer séance]

J --> K[Fin]