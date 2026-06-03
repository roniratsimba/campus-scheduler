flowchart TD

A[Début]

--> B[Préparer EDT]

--> C[Vérifier conflits]

--> D{Valide ?}

D -->|Non| E[Corriger EDT]

E --> C

D -->|Oui| F[Publier]

F --> G[Rendre visible]

G --> H[Fin]