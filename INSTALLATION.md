# Guide d'installation et de test - Campus Scheduler

## Prérequis

### Backend (Symfony)
- PHP 8.4 ou supérieur
- Composer
- SQLite (inclus avec PHP) ou PostgreSQL
- Extension PHP : pdo_sqlite ou pdo_pgsql

### Frontend (React)
- Node.js 18 ou supérieur
- npm ou yarn

## Installation

### 1. Configuration du Backend

```bash
cd backend

# Installer les dépendances
composer install

# Configurer la base de données
# Le fichier .env est déjà configuré pour SQLite
# DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_%kernel.environment%.db"

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Charger les fixtures (optionnel)
php bin/console doctrine:fixtures:load
```

### 2. Configuration du Frontend

```bash
cd frontend

# Installer les dépendances
npm install
```

### 3. Installation de l'authentification JWT (optionnel)

```bash
cd backend

# Installer le bundle
composer require lexik/jwt-authentication-bundle

# Générer les clés
php bin/console lexik:jwt:generate-keypair
```

## Démarrage de l'application

### Backend

```bash
cd backend

# Option 1: Avec Symfony CLI (recommandé)
symfony server:start --port=8000

# Option 2: Avec le serveur PHP intégré
php -S localhost:8000 -t public
```

Le backend sera accessible sur `http://localhost:8000`

### Frontend

```bash
cd frontend

# Démarrer le serveur de développement
npm run dev
```

Le frontend sera accessible sur `http://localhost:5173`

## Test de l'application

### 1. Test des endpoints API

#### Consultation publique (sans authentification)

```bash
# Lister les groupes
curl http://localhost:8000/api/public/groups

# Lister les enseignants
curl http://localhost:8000/api/public/teachers

# Lister les salles
curl http://localhost:8000/api/public/rooms

# Voir l'EDT d'un groupe
curl http://localhost:8000/api/public/schedule/group/1

# Voir l'EDT d'un enseignant
curl http://localhost:8000/api/public/schedule/teacher/1

# Voir l'EDT d'une salle
curl http://localhost:8000/api/public/schedule/room/1

# Rechercher des salles libres
curl "http://localhost:8000/api/public/rooms/free?dayOfWeek=MONDAY&startTime=08:00&endTime=10:00&weekId=1"
```

#### Endpoints administratifs (nécessitent l'authentification)

```bash
# Créer un utilisateur
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password123","role":"ROLE_ADMIN"}'

# Connexion
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password123"}'

# Lister les séances (avec JWT token)
curl http://localhost:8000/api/course-sessions \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### 2. Test de l'interface Frontend

1. Ouvrir `http://localhost:5173` dans votre navigateur
2. Vous verrez la page d'accueil avec les options de consultation
3. Sélectionner un groupe, enseignant ou salle pour voir son EDT
4. Cliquer sur "Accès administration" pour aller à la page de connexion
5. Se connecter avec les identifiants créés
6. Accéder au dashboard pour gérer les données

### 3. Fonctionnalités à tester

- **Consultation publique** : EDT par groupe, enseignant, salle
- **Recherche de salles libres** : Selon jour et créneau horaire
- **Gestion des séances** : Création, modification, suppression
- **Publication d'EDT** : Publier une semaine
- **Copie de semaine** : Copier les séances d'une semaine vers une autre
- **Détection de conflits** : Enseignant, salle, groupe

## Dépannage

### Problèmes courants

**PHP non reconnu**
- Installer PHP depuis https://windows.php.net/download/
- Ajouter PHP au PATH système

**Composer non reconnu**
- Installer Composer depuis https://getcomposer.org/download/
- Ajouter Composer au PATH système

**Node.js non reconnu**
- Installer Node.js depuis https://nodejs.org/

**Erreur de connexion à la base de données**
- Vérifier que le DATABASE_URL est correct dans .env
- Pour SQLite, s'assurer que le dossier var existe et est accessible en écriture

**Erreur CORS**
- Vérifier la configuration CORS dans config/packages/nelmio_cors.yaml
- L'URL du frontend doit être autorisée

## Structure du projet

```
campus-scheduler/
├── backend/                 # Application Symfony
│   ├── src/
│   │   ├── Controller/      # Contrôleurs API
│   │   ├── Entity/          # Entités Doctrine
│   │   ├── Repository/      # Repositories
│   │   └── Enum/            # Énumérations
│   ├── config/              # Configuration
│   ├── migrations/          # Migrations de base de données
│   └── public/              # Point d'entrée web
├── frontend/                # Application React
│   ├── src/
│   │   ├── components/      # Composants React
│   │   ├── pages/           # Pages de l'application
│   │   ├── router/          # Configuration du router
│   │   ├── service/         # Services API
│   │   └── types/           # Types TypeScript
│   └── public/              # Fichiers statiques
└── docs/                    # Documentation
    ├── architecture/        # Diagrammes d'architecture
    ├── database/            # Schéma de base de données
    └── uml/                 # Diagrammes UML
```

## Prochaines étapes

1. Installer les dépendances manquantes (PHP, Composer, Node.js)
2. Configurer la base de données
3. Exécuter les migrations
4. Démarrer les serveurs
5. Tester les fonctionnalités
6. Personnaliser l'interface selon vos besoins
