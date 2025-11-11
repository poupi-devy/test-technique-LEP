# Test Technique – Lead Développeur PHP/Symfony

Les Échos Publishing

---

## 📋 Table des matières

1. [Introduction](#introduction)
2. [Stack Technique](#stack-technique)
3. [Structure du projet](#structure-du-projet)
4. [Sections du test](#sections-du-test)
5. [Installation](#installation)

---

## Introduction

Ce repository contient les réponses au test technique pour le poste de **Lead Développeur PHP/Symfony** chez Les Échos Publishing.

---

## Stack Technique

- **PHP**: 8.4 (typage fort)
- **Symfony**: 7.3
- **PostgreSQL**: 12+
- **PHPStan**: Analyse statique (level max)
- **Psalm**: Type checking
- **ECS**: Standards PSR-12
- **Rector**: Refactoring PHP 8.4
- **PHPUnit**: Tests
- **GitHub Actions**: CI/CD

---

## Structure du projet

```
.
├── README.md
├── test-todo.md
├── .gitignore
├── docs/                          # Réponses aux questions (6 sections)
│   ├── I-fondamentaux-php-symfony/
│   ├── II-bases-donnees/
│   ├── III-conception-api/
│   ├── IV-qualite-code/
│   ├── V-devops-infrastructure/
│   └── VI-leadership-architecture/
├── exercises/                     # Code des 3 exercices pratiques
│   ├── 01-commande-csv/
│   │   ├── src/
│   │   └── tests/
│   ├── 02-requete-sql/
│   └── 03-endpoint-api/
│       ├── src/
│       └── tests/
├── config/                        # Configuration Symfony (projet principal)
└── .github/workflows/             # GitHub Actions CI/CD
```

**Séparation claire**:
- **docs/**: Réponses textuelles aux questions (conceptuelles et scénarios)
- **exercises/**: Code source et tests des 3 exercices pratiques

---

## I. Fondamentaux PHP et Symfony

### Questions conceptuelles

| # | Question | Lien |
|---|----------|------|
| 1 | Interface vs Classe abstraite | [docs/I-fondamentaux-php-symfony/questions-conceptuelles.md#q1](./docs/I-fondamentaux-php-symfony/questions-conceptuelles.md#q1) |
| 2 | Principes SOLID | [docs/I-fondamentaux-php-symfony/questions-conceptuelles.md#q2](./docs/I-fondamentaux-php-symfony/questions-conceptuelles.md#q2) |
| 3 | Design patterns | [docs/I-fondamentaux-php-symfony/questions-conceptuelles.md#q3](./docs/I-fondamentaux-php-symfony/questions-conceptuelles.md#q3) |
| 4 | Injection de dépendances (DI) | [docs/I-fondamentaux-php-symfony/questions-conceptuelles.md#q4](./docs/I-fondamentaux-php-symfony/questions-conceptuelles.md#q4) |
| 5 | Event dispatcher Symfony | [docs/I-fondamentaux-php-symfony/questions-conceptuelles.md#q5](./docs/I-fondamentaux-php-symfony/questions-conceptuelles.md#q5) |
| 6 | CSRF Tokens | [docs/I-fondamentaux-php-symfony/questions-conceptuelles.md#q6](./docs/I-fondamentaux-php-symfony/questions-conceptuelles.md#q6) |

### Connaissance des composants Symfony

| Composant | Lien |
|-----------|------|
| Routage | [docs/I-fondamentaux-php-symfony/composants-symfony.md#routage](./docs/I-fondamentaux-php-symfony/composants-symfony.md#routage) |
| Services | [docs/I-fondamentaux-php-symfony/composants-symfony.md#services](./docs/I-fondamentaux-php-symfony/composants-symfony.md#services) |
| Formulaires | [docs/I-fondamentaux-php-symfony/composants-symfony.md#formulaires](./docs/I-fondamentaux-php-symfony/composants-symfony.md#formulaires) |

### Exercice pratique : Commande Symfony

**Scénario**: Importer des données libraires (titre, auteur, année, ISBN) à partir d'un fichier CSV

**Réponse**: [docs/I-fondamentaux-php-symfony/exercice-commande-csv.md](./docs/I-fondamentaux-php-symfony/exercice-commande-csv.md)

**Code source**: [exercises/01-commande-csv/](./exercises/01-commande-csv/)

---

## II. Connaissance des bases de données

### Questions conceptuelles

| # | Question | Lien |
|---|----------|------|
| 1 | Relationnelles vs Non-relationnelles | [docs/II-bases-donnees/questions-conceptuelles.md#q1](./docs/II-bases-donnees/questions-conceptuelles.md#q1) |
| 2 | Formes de normalisation (1NF, 2NF, 3NF) | [docs/II-bases-donnees/questions-conceptuelles.md#q2](./docs/II-bases-donnees/questions-conceptuelles.md#q2) |
| 3 | Index et optimisation | [docs/II-bases-donnees/questions-conceptuelles.md#q3](./docs/II-bases-donnees/questions-conceptuelles.md#q3) |
| 4 | Transactions et cohérence | [docs/II-bases-donnees/questions-conceptuelles.md#q4](./docs/II-bases-donnees/questions-conceptuelles.md#q4) |
| 5 | Techniques d'optimisation | [docs/II-bases-donnees/questions-conceptuelles.md#q5](./docs/II-bases-donnees/questions-conceptuelles.md#q5) |

### Exercice pratique : Requête SQL

**Scénario**: Tables `users` et `orders` – récupérer les utilisateurs avec commandes > 100€

**Réponse**: [docs/II-bases-donnees/exercice-requete-sql.md](./docs/II-bases-donnees/exercice-requete-sql.md)

**Code source**: [exercises/02-requete-sql/](./exercises/02-requete-sql/)

---

## III. Conception et développement d'API

### Questions conceptuelles

| # | Question | Lien |
|---|----------|------|
| 1 | Principes RESTful | [docs/III-conception-api/questions-conceptuelles.md#q1](./docs/III-conception-api/questions-conceptuelles.md#q1) |
| 2 | Méthodes HTTP (GET, POST, PUT, DELETE) | [docs/III-conception-api/questions-conceptuelles.md#q2](./docs/III-conception-api/questions-conceptuelles.md#q2) |
| 3 | Versionnage d'API | [docs/III-conception-api/questions-conceptuelles.md#q3](./docs/III-conception-api/questions-conceptuelles.md#q3) |
| 4 | Authentification (API Key, OAuth, JWT) | [docs/III-conception-api/questions-conceptuelles.md#q4](./docs/III-conception-api/questions-conceptuelles.md#q4) |
| 5 | Rate limiting | [docs/III-conception-api/questions-conceptuelles.md#q5](./docs/III-conception-api/questions-conceptuelles.md#q5) |

### Exercice pratique : Endpoint API

**Scénario**: Endpoint pour créer un produit (nom, description, prix, catégorie)

**Réponse**: [docs/III-conception-api/exercice-endpoint-api.md](./docs/III-conception-api/exercice-endpoint-api.md)

**Code source**: [exercises/03-endpoint-api/](./exercises/03-endpoint-api/)

---

## IV. Qualité du code et bonnes pratiques

### Questions conceptuelles

| # | Question | Lien |
|---|----------|------|
| 1 | Mesures de qualité du code | [docs/IV-qualite-code/questions-conceptuelles.md#q1](./docs/IV-qualite-code/questions-conceptuelles.md#q1) |
| 2 | Types de tests (unitaire, intégration, fonctionnel) | [docs/IV-qualite-code/questions-conceptuelles.md#q2](./docs/IV-qualite-code/questions-conceptuelles.md#q2) |
| 3 | Revue de code | [docs/IV-qualite-code/questions-conceptuelles.md#q3](./docs/IV-qualite-code/questions-conceptuelles.md#q3) |

### Scénarios

| # | Scénario | Lien |
|---|----------|------|
| 1 | Déboguer une application Symfony lente | [docs/IV-qualite-code/questions-conceptuelles.md#s1](./docs/IV-qualite-code/questions-conceptuelles.md#s1) |
| 2 | Corriger et déployer un bug en production | [docs/IV-qualite-code/questions-conceptuelles.md#s2](./docs/IV-qualite-code/questions-conceptuelles.md#s2) |

---

## V. DevOps et Infrastructure

### Questions conceptuelles

| # | Question | Lien |
|---|----------|------|
| 1 | Avantages de Docker | [docs/V-devops-infrastructure/questions-conceptuelles.md#q1](./docs/V-devops-infrastructure/questions-conceptuelles.md#q1) |
| 2 | Git et GitHub – flux de travail | [docs/V-devops-infrastructure/questions-conceptuelles.md#q2](./docs/V-devops-infrastructure/questions-conceptuelles.md#q2) |
| 3 | CI/CD et pipelines | [docs/V-devops-infrastructure/questions-conceptuelles.md#q3](./docs/V-devops-infrastructure/questions-conceptuelles.md#q3) |

### Tâche de déploiement

**Objectif**: Déploiement d'une application Symfony sur Google Cloud Platform (GCP)

**Réponse**: [docs/V-devops-infrastructure/deploiement-gcp.md](./docs/V-devops-infrastructure/deploiement-gcp.md)

---

## VI. Leadership et Architecture

### Questions situationnelles

| # | Question | Lien |
|---|----------|------|
| 1 | Monolithique vs Micro-services | [docs/VI-leadership-architecture/questions-situationnelles.md#q1](./docs/VI-leadership-architecture/questions-situationnelles.md#q1) |
| 2 | Gestion d'un développeur produisant du code de mauvaise qualité | [docs/VI-leadership-architecture/questions-situationnelles.md#q2](./docs/VI-leadership-architecture/questions-situationnelles.md#q2) |
| 3 | Faciliter une discussion sur deux approches architecturales | [docs/VI-leadership-architecture/questions-situationnelles.md#q3](./docs/VI-leadership-architecture/questions-situationnelles.md#q3) |
| 4 | Dernier défi technique relevé | [docs/VI-leadership-architecture/questions-situationnelles.md#q4](./docs/VI-leadership-architecture/questions-situationnelles.md#q4) |

---

## Installation et utilisation

### Prérequis

- PHP 8.4+
- Symfony 7.3
- PostgreSQL 12+
- Composer
- Docker (optionnel)

### Installation

```bash
# Cloner le repository
git clone <repository-url>
cd test-technique-LEP

# Installer les dépendances
composer install

# Configuration
cp .env.example .env

# Base de données
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### Tests

```bash
# Tous les tests
php bin/phpunit

# Tests unitaires
php bin/phpunit tests/Unit

# Tests fonctionnels
php bin/phpunit tests/Functional

# Couverture de code
php bin/phpunit --coverage-html=coverage
```

### Exécuter les exercices

#### I. Commande d'importation CSV
```bash
php bin/console app:import:books data/books.csv
```

#### II. Requête SQL
Consultez [src/Database/queries.sql](./src/Database/queries.sql)

#### III. Endpoint API
```bash
php bin/console server:run
# POST /api/v1/products
```

---

## 📊 Stratégie de tests

Voir [test-todo.md](./test-todo.md) pour la stratégie complète de test incluant:
- Approche de test (unitaire, fonctionnel, intégration)
- Ordre d'exécution
- Cas de test couverts
- Métriques de couverture

---

## Principes du Code

- **Typage fort**: PHP 8.4 avec type hints obligatoires
- **SOLID**: Single Responsibility, Open/Closed, Liskov, Interface Segregation, Dependency Inversion
- **DRY**: Pas de duplication, réutilisabilité maximale
- **Code propre**: Bien organisé, séparation des responsabilités
- **Tests**: Couverture complète (unitaires, fonctionnels, intégration)
- **Constantes et Enums**: Pas de magic numbers

---