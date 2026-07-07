# Guide d'initialisation de la base de données PostgreSQL

## Prérequis

- PostgreSQL 16 installé sur votre système
- Accès au serveur PostgreSQL (localhost ou distant)
- Utilisateur avec droits de création de base de données

## Installation de PostgreSQL

### Windows
1. Téléchargez PostgreSQL depuis https://www.postgresql.org/download/windows/
2. Installez avec les paramètres par défaut
3. Notez le mot de passe de l'utilisateur `postgres` configuré lors de l'installation

### Linux (Ubuntu/Debian)
```bash
sudo apt update
sudo apt install postgresql postgresql-contrib
sudo systemctl start postgresql
sudo systemctl enable postgresql
```

### macOS
```bash
brew install postgresql@16
brew services start postgresql@16
```

## Configuration de la base de données

### 1. Créer la base de données

#### Option A: Via psql en ligne de commande
```bash
# Connectez-vous à PostgreSQL
psql -U postgres

# Créez la base de données
CREATE DATABASE campus_scheduler;

# Quittez psql
\q
```

#### Option B: Via pgAdmin
1. Ouvrez pgAdmin
2. Connectez-vous à votre serveur PostgreSQL
3. Cliquez droit sur "Databases" → "Create" → "Database"
4. Nommez la base de données `campus_scheduler`
5. Cliquez sur "Save"

### 2. Exécuter le script d'initialisation

#### Via psql en ligne de commande
```bash
cd backend/database
psql -U postgres -d campus_scheduler -f init.sql
```

Si votre utilisateur PostgreSQL a un mot de passe différent :
```bash
psql -U postgres -d campus_scheduler -f init.sql -W
```

#### Via pgAdmin
1. Ouvrez pgAdmin
2. Connectez-vous à votre serveur PostgreSQL
3. Développez "Databases" → "campus_scheduler"
4. Cliquez droit sur "campus_scheduler" → "Query Tool"
5. Ouvrez le fichier `backend/database/init.sql`
6. Copiez tout le contenu SQL
7. Collez dans le Query Tool
8. Cliquez sur "Execute" (ou F5)

## Vérification de l'installation

Après l'exécution du script, vous pouvez vérifier que les données ont été insérées :

```sql
-- Vérifier les tables
SELECT table_name FROM information_schema.tables 
WHERE table_schema = 'public' 
ORDER BY table_name;

-- Vérifier les données
SELECT COUNT(*) FROM level;
SELECT COUNT(*) FROM program;
SELECT COUNT(*) FROM academic_group;
SELECT COUNT(*) FROM teacher;
SELECT COUNT(*) FROM subject;
SELECT COUNT(*) FROM room;
SELECT COUNT(*) FROM time_slot;
SELECT COUNT(*) FROM schedule_week;
SELECT COUNT(*) FROM course_session;
SELECT COUNT(*) FROM users;
```

## Configuration de l'application Symfony

Le fichier `.env` est déjà configuré pour PostgreSQL :

```
DATABASE_URL="postgresql://postgres:postgres@127.0.0.1:5432/campus_scheduler?serverVersion=16&charset=utf8"
```

Si vous avez configuré PostgreSQL avec des paramètres différents, modifiez cette ligne :

```
DATABASE_URL="postgresql://UTILISATEUR:MOT_DE_PASSE@HOST:PORT/NOM_BD?serverVersion=16&charset=utf8"
```

Exemple :
```
DATABASE_URL="postgresql://admin:mon_password@localhost:5432/campus_scheduler?serverVersion=16&charset=utf8"
```

## Données de démonstration incluses

Le script inclut les données de démonstration suivantes :

### Niveaux académiques (5)
- L1, L2, L3 (Licence)
- M1, M2 (Master)

### Programmes d'études (5)
- GB: Génie Logiciel
- SR: Systèmes et Réseaux
- ASI: Administration Systèmes et Infrastructures
- IA: Intelligence Artificielle
- DS: Data Science

### Groupes académiques (6)
- L2 GB Groupe 1 et 2
- L3 SR Groupe 1
- L3 ASI Groupe 1
- M1 IA Groupe 1
- M1 DS Groupe 1

### Enseignants (6)
- Siaka, Jean Rakoto, Marie Dupont, Pierre Martin, Sophie Bernard, Luc Petit

### Matières (10)
- Algorithmique, Base de données, Réseaux, Développement Web
- Intelligence Artificielle, Machine Learning, Sécurité Informatique
- DevOps, Cloud Computing, Statistiques

### Salles (7)
- A101, A102, A103 (Salles de classe)
- B201, B202 (Laboratoires)
- C301, C302 (Amphithéâtres)

### Créneaux horaires (20)
- Du lundi au vendredi
- 4 créneaux par jour : 08:00-10:00, 10:00-12:00, 14:00-16:00, 16:00-18:00

### Semaines d'emploi du temps (4)
- 2 semaines publiées
- 2 semaines en brouillon

### Séances de cours (10)
- 10 séances réparties sur la semaine 1
- Couvrant différentes matières et groupes

### Utilisateur admin (1)
- Email: admin@campus.local
- Mot de passe: admin123 (à changer en production)
- Rôle: ROLE_ADMIN

## Réinitialisation de la base de données

Pour réinitialiser complètement la base de données :

```bash
# Supprimer et recréer la base de données
psql -U postgres -c "DROP DATABASE IF EXISTS campus_scheduler;"
psql -U postgres -c "CREATE DATABASE campus_scheduler;"

# Réexécuter le script d'initialisation
cd backend/database
psql -U postgres -d campus_scheduler -f init.sql
```

## Dépannage

### Erreur "connection refused"
- Vérifiez que PostgreSQL est en cours d'exécution
- Vérifiez que le port 5432 est correct
- Vérifiez les paramètres de connexion dans `pg_hba.conf`

### Erreur "password authentication failed"
- Vérifiez le mot de passe de l'utilisateur PostgreSQL
- Modifiez le `DATABASE_URL` dans `.env` avec les bons identifiants

### Erreur "database does not exist"
- Créez d'abord la base de données avec `CREATE DATABASE campus_scheduler;`
- Vérifiez que vous vous connectez à la bonne base de données

## Prochaines étapes

1. Exécutez le script SQL
2. Vérifiez que les données sont correctement insérées
3. Redémarrez le serveur Symfony
4. Testez l'application avec les données de démonstration
