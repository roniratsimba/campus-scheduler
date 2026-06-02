# ADR-001 : Choix de la stack technologique

## Statut

Accepté

## Date

2026-06-02

## Contexte

Le projet Campus Scheduler est une application web de gestion d'emploi du temps universitaire.

L'objectif est :

- gérer les séances de cours ;
- gérer les salles ;
- publier les emplois du temps hebdomadaires ;
- permettre la consultation publique des emplois du temps.

Le projet est également conçu comme support d'apprentissage pour un stage orienté développement web moderne.

---

## Décision

### Backend

Symfony

### Frontend

React

### Base de données

PostgreSQL

### Communication

API REST JSON

### Gestion de version

Git + GitHub

### Documentation

Markdown + Mermaid + DBML

---

## Justification

### Symfony

- Très utilisé en entreprise.
- Framework PHP robuste.
- Architecture claire.
- ORM Doctrine intégré.
- Bon choix pour un futur stage.

### React

- Très demandé sur le marché.
- Permet d'apprendre le développement frontend moderne.
- Compatible avec une API REST Symfony.

### PostgreSQL

- SGBD robuste.
- Open source.
- Très utilisé dans les projets professionnels.
- Bonne gestion des contraintes relationnelles.

### API REST

- Séparation frontend/backend.
- Architecture moderne.
- Facilement extensible.

### GitHub

- Standard industriel.
- Collaboration.
- Historique du projet.

---

## Conséquences

### Positives

- Stack moderne.
- Bonne valeur pédagogique.
- Cohérence avec les objectifs de stage.

### Négatives

- Courbe d'apprentissage plus importante.
- Nécessite d'apprendre React en parallèle de Symfony.