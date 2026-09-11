# Guide d'installation et de test - Campus Scheduler

## Prérequis

### Backend (Symfony)
- PHP 8.4 ou supérieur
- Composer 2
- PostgreSQL 16 (service démarré, port 5432)
- Extension PHP : `pdo_pgsql`

### Frontend (React)
- Node.js 18 ou supérieur
- npm ou yarn

## Installation

### 1. Configuration du Backend

```bash
cd backend

# Installer les dépendances
composer install
```

#### Base de données PostgreSQL

Le projet utilise **PostgreSQL** (et non SQLite). Deux façons d'initialiser la base, identiques dans le résultat du schéma :

**Option A — script SQL (recommandé, inclut les données de démonstration) :**

```bash
# Base de données (à faire une fois)
psql -U postgres -c "CREATE DATABASE campus_scheduler;"

# Création du schéma + jeu de données de démo
psql -U postgres -d campus_scheduler -f backend/database/init.sql
```

**Option B — migrations Doctrine (schéma seul) :**

```bash
php bin/console doctrine:migrations:migrate
```

#### Connexion

Vérifiez le `DATABASE_URL` dans `backend/.env` (ou surchargez-le dans `backend/.env.local`) :

```
DATABASE_URL="postgresql://POSTGRES_USER:PASSWORD@127.0.0.1:5432/campus_scheduler?serverVersion=16&charset=utf8"
```

### 2. Configuration du Frontend

```bash
cd frontend

npm install
```

Par défaut, l'API est appelée sur `http://127.0.0.1:8000/api`. Pour surcharger :

```bash
# copier .env.example -> .env.local puis adapter si besoin
# VITE_API_URL=http://127.0.0.1:8000/api
```

### 3. Authentification (état actuel)

Le JWT n'est **pas encore** implémenté : `POST /api/login` renvoie un jeton *placeholder*. Les routes d'administration du frontend sont protégées par une garde (`RequireAuth`) basée sur la présence du jeton en `localStorage`.

Pour une vraie authentification, installer ensuite `lexik/jwt-authentication-bundle` et activer `jwt` dans le firewall `main` de `config/packages/security.yaml`.

## Données de démonstration

Le script `backend/database/init.sql` insère : 5 niveaux, 5 programmes, 6 groupes, 6 enseignants, 10 matières, 7 salles, 20 créneaux, 4 semaines (2 publiées, 2 brouillons), 10 séances et l'administrateur :

| Rôle | Email | Mot de passe |
| --- | --- | --- |
| Administrateur | `admin@campus.local` | `admin123` |

> À changer avant toute mise en production.

## Démarrage de l'application

### Backend

```bash
cd backend

# Option 1 : Symfony CLI (recommandé)
symfony server:start --port=8000

# Option 2 : serveur PHP intégré
php -S 127.0.0.1:8000 -t public
```

Le backend est accessible sur `http://127.0.0.1:8000`.

### Frontend

```bash
cd frontend
npm run dev
```

Le frontend est accessible sur `http://localhost:5173`.

## Test de l'application

### 1. Test des endpoints API

#### Consultation publique (sans authentification)

```bash
# Lister les groupes / enseignants / salles
curl http://localhost:8000/api/public/groups
curl http://localhost:8000/api/public/teachers
curl http://localhost:8000/api/public/rooms

# EDT d'un groupe, d'un enseignant, d'une salle (semaines publiées uniquement)
curl http://localhost:8000/api/public/schedule/group/1
curl http://localhost:8000/api/public/schedule/teacher/1
curl http://localhost:8000/api/public/schedule/room/1

# Salles libres (chevauchement de créneau, séances ONLINE ignorées)
curl "http://localhost:8000/api/public/rooms/free?dayOfWeek=MONDAY&startTime=08:00&endTime=10:00&weekId=1"
```

#### Authentification et endpoints administratifs

```bash
# Connexion (renvoie { user, token } — jeton placeholder)
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@campus.local","password":"admin123"}'

# Inscription
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password123","role":"ROLE_ADMIN"}'

# CRUD (enseignants, matières, salles, groupes, semaines, séances)
curl http://localhost:8000/api/course-sessions \
  -H "Authorization: Bearer YOUR_TOKEN"

# Publish une semaine de brouillon
curl -X POST http://localhost:8000/api/schedule-weeks/3/publish
```

### 2. Test de l'interface Frontend

1. Ouvrir `http://localhost:5173` : page d'accueil avec consultation publique (EDT par groupe/enseignant/salle, salles libres).
2. Cliquer sur « Accès administration » → `/login`.
3. Se connecter avec `admin@campus.local` / `admin123`.
4. Accéder au dashboard : gestion des enseignants, matières, salles, groupes, semaines, séances (grille avec sélecteur de semaine).

### 3. Fonctionnalités à tester

- **Consultation publique** : EDT par groupe, enseignant, salle (uniquement les semaines publiées).
- **Recherche de salles libres** : selon jour + créneau (chevauchement géré).
- **Gestion des séances** : création (grille + modal), avec détection de conflits enseignant/salle/groupe, mode ONLINE sans salle, PRESENTIAL avec salle obligatoire.
- **Semaines** : création, copie entre semaines, publication avec validation des conflits ; une semaine publiée devient immuable (BR-004).

## Dépannage

### Connexion à la base de données
- Vérifier que PostgreSQL est démarré (port 5432) et que `DATABASE_URL` (`.env` ou `.env.local`) est correct.

### « Violation de contrainte unique … (id)=(1) » à la création d'un enregistrement
- Les séquences des colonnes `GENERATED BY DEFAULT AS IDENTITY` ne sont pas synchronisées avec les données importées. Les resynchroniser :
  ```sql
  SELECT setval(pg_get_serial_sequence('teacher', 'id'), (SELECT COALESCE(MAX(id), 1) FROM teacher));
  ```
  (recommencer pour chaque table). Réinitialiser proprement en relançant `backend/database/init.sql` sur une base vide.

### Caractères accentués affichés en double (« AmphithÃ©Ã¢tre »)
- Données importées avec un mauvais encodage client. Réimporter avec `PGCLIENTENCODING=UTF8`, ou corriger les lignes concernées.

### Erreur CORS
- Vérifier `config/packages/nelmio_cors.yaml` : l'origine du frontend (ex. `http://localhost:5173`) doit être autorisée.

### Commandes utiles
```bash
cd backend
php bin/console cache:clear
php bin/console lint:container
php bin/console doctrine:schema:validate --skip-sync

cd frontend
npm run build
npm run lint
```

## Structure du projet

```
campus-scheduler/
├── backend/                 # Application Symfony (API REST)
│   ├── config/              # Configuration (routes, sécurité, doctrine…)
│   ├── database/            # init.sql (schéma + démo PostgreSQL)
│   ├── migrations/          # Migrations Doctrine
│   ├── public/              # Point d'entrée web
│   └── src/
│       ├── Controller/      # Contrôleurs API (admin + public)
│       ├── Entity/          # Entités Doctrine
│       ├── Enum/            # Énumérations (DeliveryMode…)
│       └── Repository/      # Accès aux données + validation des conflits
├── frontend/                # Application React (SPA)
│   └── src/
│       ├── components/      # Layout, Sidebar, RequireAuth, Modal…
│       ├── pages/           # Pages (Home, Login, Dashboard, EDT publics…)
│       ├── router/          # Configuration du router
│       └── service/         # Client API (axios + interceptor auth)
├── docs/                    # Documentation (ADR, architecture, db, UML)
└── git_utils.py             # Utilitaires Git (scripts de maintenance)
```