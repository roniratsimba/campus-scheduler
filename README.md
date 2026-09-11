# Campus Scheduler

Application web de gestion d'emplois du temps universitaires : planification des séances de cours, gestion des salles, des enseignants et des groupes, et consultation publique des emplois du temps publiés.

- **Backend** : Symfony 7 / PHP 8.4 + Doctrine ORM + PostgreSQL 16
- **Frontend** : React 19 + TypeScript + Vite + axios
- **Communication** : API REST JSON (`/api`)

## Démarrage rapide

Voir [INSTALLATION.md](INSTALLATION.md) pour l'installation complète et les tests.

## Documentation

### Architecture

- [Choix technologiques (ADR-001)](docs/adr/adr-001-technology-stack.md)
- [Règles métier (BR-001 à BR-006)](docs/adr/business-rules.md)
- [Architecture backend](docs/architecture/backend.md)
- [Architecture frontend](docs/architecture/frontend.md)
- [Sitemap](docs/architecture/sitemap-global.md)

### Modèle de données

- [Modèle Conceptuel de Données (MCD)](docs/database/mcd.md)
- [Modèle Logique de Données (MLD)](docs/database/mld.md)
- [Dictionnaire de données](docs/database/data-dictionnary.md)
- [Schéma DBML (export)](docs/database/campus_scheduler_v1.dbml)
- [Initialisation PostgreSQL + données de démonstration](backend/database/README.md)

### UML

- [Use cases](docs/uml/use-cases.md)
- [Diagramme de classes](docs/uml/diagram-class.md)
- [Diagrammes d'activité](docs/uml/diagram-act-create-seance.md), [](docs/uml/diagram-act-publish.md)
- [Diagrammes de séquence](docs/uml/seq-consulting.md), [](docs/uml/seq-creation-seance.md), [](docs/uml/seq-publish.md), [](docs/uml/seq-find-free-room.md)

### Diagrammes (assets)

- MCD et UML : [`docs/assets/`](docs/assets)

## Structure du projet

```
campus-scheduler/
├── backend/                 # Application Symfony (API REST)
│   ├── config/              # Configuration (routes, sécurité, doctrine…)
│   ├── database/            # Script d'initialisation PostgreSQL (init.sql)
│   ├── migrations/          # Migrations Doctrine (schéma alternatif)
│   ├── public/              # Point d'entrée web
│   └── src/
│       ├── Controller/      # Contrôleurs API (admin + public)
│       ├── Entity/          # Entités Doctrine
│       ├── Enum/            # Énumérations (DeliveryMode…)
│       └── Repository/      # Accès aux données
├── frontend/                # Application React (SPA)
│   ├── src/
│   │   ├── components/      # Composants (Layout, Sidebar, RequireAuth…)
│   │   ├── pages/           # Pages (Login, Dashboard, EDT public…)
│   │   ├── router/          # Configuration du router
│   │   └── service/         # Client API (axios + interceptor auth)
│   ├── index.html
│   └── package.json
├── docs/                    # Documentation technique
└── git_utils.py             # Petits utilitaires Git
```

## Comptes de démonstration

| Rôle | Email | Mot de passe |
| --- | --- | --- |
| Administrateur | `admin@campus.local` | `admin123` |

> ⚠️ Ce compte est fourni en démonstration uniquement : à changer avant toute mise en production.