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
- **PostgreSQL**: 16
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
├── .gitignore
├── docs/                          # Réponses aux questions (6 sections)
│   ├── I-fondamentaux-php-symfony/
│   ├── II-bases-donnees/
│   ├── III-conception-api/
│   ├── IV-qualite-code/
│   ├── V-devops-infrastructure/
│   └── VI-leadership-architecture/
├── exercices/                     # Code des 3 exercices pratiques
│   └── app/
│       ├── src/
│       │   ├── Command/
│       │   ├── Controller/
│       │   ├── DTO/
│       │   ├── Entity/
│       │   ├── Event/
│       │   ├── Service/
│       │   ├── Parser/
│       │   └── Repository/
│       └── tests/
```

**Séparation claire**:
- **docs/**: Réponses textuelles aux questions (conceptuelles et scénarios)
- **exercices/app**: Application Symfony avec les 3 exercices pratiques

---

## I. Fondamentaux PHP et Symfony

### Questions conceptuelles

| # | Question | Lien                                                                                                                                                           |
|---|----------|----------------------------------------------------------------------------------------------------------------------------------------------------------------|
| 1 | Interface vs Classe abstraite | [docs/I-fondamentaux-php-symfony/questions-conceptuelles.md#q1](./docs/I-fondamentaux-php-symfony/questions-conceptuelles.md#q1-interface-vs-classe-abstraite) |
| 2 | Principes SOLID | [docs/I-fondamentaux-php-symfony/questions-conceptuelles.md#q2](./docs/I-fondamentaux-php-symfony/questions-conceptuelles.md#q2)                               |
| 3 | Design patterns | [docs/I-fondamentaux-php-symfony/questions-conceptuelles.md#q3](./docs/I-fondamentaux-php-symfony/questions-conceptuelles.md#q3)                               |
| 4 | Injection de dépendances (DI) | [docs/I-fondamentaux-php-symfony/questions-conceptuelles.md#q4](./docs/I-fondamentaux-php-symfony/questions-conceptuelles.md#q4-injection-de-dépendances-di)   |
| 5 | Event dispatcher Symfony | [docs/I-fondamentaux-php-symfony/questions-conceptuelles.md#q5](./docs/I-fondamentaux-php-symfony/questions-conceptuelles.md#q5-event-dispatcher-symfony)      |
| 6 | CSRF Tokens | [docs/I-fondamentaux-php-symfony/questions-conceptuelles.md#q6](./docs/I-fondamentaux-php-symfony/questions-conceptuelles.md#q6-csrf-tokens)                   |

### Connaissance des composants Symfony

| Composant | Lien |
|-----------|------|
| Routage | [docs/I-fondamentaux-php-symfony/composants-symfony.md#routage](./docs/I-fondamentaux-php-symfony/composants-symfony.md#routage) |
| Services | [docs/I-fondamentaux-php-symfony/composants-symfony.md#services](./docs/I-fondamentaux-php-symfony/composants-symfony.md#services) |
| Formulaires | [docs/I-fondamentaux-php-symfony/composants-symfony.md#formulaires](./docs/I-fondamentaux-php-symfony/composants-symfony.md#formulaires) |

### Exercice pratique 1 : Commande Symfony

**Scénario**: Importer des données libraires (titre, auteur, année, ISBN) à partir d'un fichier CSV

**Réponse**: [docs/I-fondamentaux-php-symfony/exercice-commande-csv.md](./docs/I-fondamentaux-php-symfony/exercice-commande-csv.md)

**Code source**: [exercices/app/src/Command/ImportBooksCommand.php](./exercices/app/src/Command/ImportBooksCommand.php)

#### Implémentation

**Architecture:**
- `ImportBooksCommand`: Commande console pour orchestrer l'import
- `CsvFileParser`: Parser générique pour lecture CSV (extensible à d'autres formats)
- `BookImportHydrator`: Conversion ligne CSV → DTO `BookImportData`
- `BookValidator`: Validation avec Symfony Validator
- `BookPersister`: Persistance en base de données
- `ImportBooksService`: Orchestration du workflow

**Tests unitaires:**
- `BookImportHydratorTest` (5 tests): Hydration avec cas limites (valeurs nulles, colonnes manquantes, etc.)
- `BookPersisterTest` (6 tests): Persistance Doctrine et opérations de flush
- `ValidatorWrapperTest` (7 tests): Logique de validation avec contraintes métier

Exécuter:
```bash
php bin/phpunit tests/Unit/Service/BookImportHydratorTest.php
php bin/phpunit tests/Unit/Service/BookPersisterTest.php
php bin/phpunit tests/Unit/Service/ValidatorWrapperTest.php
```

---

## II. Connaissance des bases de données

### Questions conceptuelles

| # | Question | Lien |
|---|----------|------|
| 1 | Relationnelles vs Non-relationnelles | [docs/II-bases-donnees/questions-conceptuelles.md#q1](./docs/II-bases-donnees/questions-conceptuelles.md#q1-bases-de-donnees-relationnelles-vs-non-relationnelles) |
| 2 | Formes de normalisation (1NF, 2NF, 3NF) | [docs/II-bases-donnees/questions-conceptuelles.md#q2](./docs/II-bases-donnees/questions-conceptuelles.md#q2-formes-de-normalisation-1nf-2nf-3nf) |
| 3 | Index et optimisation | [docs/II-bases-donnees/questions-conceptuelles.md#q3](./docs/II-bases-donnees/questions-conceptuelles.md#q3-index-dans-une-base-de-donnees) |
| 4 | Transactions et cohérence | [docs/II-bases-donnees/questions-conceptuelles.md#q4](./docs/II-bases-donnees/questions-conceptuelles.md#q4-transactions-de-base-de-donnees) |
| 5 | Techniques d'optimisation | [docs/II-bases-donnees/questions-conceptuelles.md#q5](./docs/II-bases-donnees/questions-conceptuelles.md#q5-techniques-courantes-doptimisation) |

### Exercice pratique : Requête SQL

**Scénario**: Tables `users` et `orders` – récupérer les utilisateurs avec commandes > 100€

**Réponse**: [exercices/02-requete-sql/README.md](./exercices/02-requete-sql/README.md)

---

## III. Conception et développement d'API

### Questions conceptuelles

| # | Question | Lien |
|---|----------|------|
| 1 | Principes RESTful | [docs/III-conception-api/questions-conceptuelles.md#q1](./docs/III-conception-api/questions-conceptuelles.md#q1-principes-clés-de-la-conception-restful) |
| 2 | Méthodes HTTP (GET, POST, PUT, DELETE) | [docs/III-conception-api/questions-conceptuelles.md#q2](./docs/III-conception-api/questions-conceptuelles.md#q2-méthodes-http-get-post-put-delete) |
| 3 | Versionnage d'API | [docs/III-conception-api/questions-conceptuelles.md#q3](./docs/III-conception-api/questions-conceptuelles.md#q3-versionnage-dapi) |
| 4 | Authentification (API Key, OAuth, JWT) | [docs/III-conception-api/questions-conceptuelles.md#q4](./docs/III-conception-api/questions-conceptuelles.md#q4-authentification-et-sécurisation) |
| 5 | Rate limiting | [docs/III-conception-api/questions-conceptuelles.md#q5](./docs/III-conception-api/questions-conceptuelles.md#q5-rate-limiting-limitation-du-débit) |

### Exercice pratique 3 : Endpoint API

**Scénario**: Endpoint pour créer un produit (nom, description, prix, catégorie)

**Réponse**: [docs/III-conception-api/exercice-endpoint-api.md](./docs/III-conception-api/exercice-endpoint-api.md)

**Code source**: [exercices/app/src/Controller/Api/ProductController.php](./exercices/app/src/Controller/Api/ProductController.php)

#### Implémentation

**Architecture:**
- `ProductController`: Endpoint API RESTful `POST /api/v1/products`
- `ProductCreateRequest`: DTO pour la requête avec validation
- `Product`: Entity Doctrine avec typage fort
- `ProductHydrator`: Conversion DTO → Entity
- `CreateProductService`: Logique métier d'orchestration
- `PersistProductHandler`: Event handler pour la persistance
- `ApiErrorResponse`: DTO pour les réponses d'erreur cohérentes

**API Endpoints:**
```
POST /api/v1/products
Content-Type: application/json

Request:
{
  "name": "Laptop",
  "price": "999.99",
  "categoryId": 1,
  "description": "A powerful laptop"
}

Response 201:
{
  "id": 1,
  "name": "Laptop",
  "price": "999.99",
  "categoryId": 1,
  "description": "A powerful laptop",
  "createdAt": "2025-11-11T12:00:00+00:00"
}

Response 422 (Validation Error):
{
  "error": "validation_failed",
  "message": "Product creation failed. Please check the violations below.",
  "violations": [
    {"field": "name", "message": "Title must be at least 3 characters"}
  ]
}
```

**Tests unitaires:**
- `ProductHydratorTest` (5 tests): Hydration de Product avec gestion de description optionnelle
- `PersistProductHandlerTest` (3 tests): Event handler pour la persistance en base
- Validation complète via `ValidatorWrapperTest` (7 tests)

Exécuter:
```bash
php bin/phpunit tests/Unit/Service/ProductHydratorTest.php
php bin/phpunit tests/Unit/EventHandler/PersistProductHandlerTest.php
```

---

## IV. Qualité du code et bonnes pratiques

### Questions conceptuelles

| # | Question | Lien |
|---|----------|------|
| 1 | Mesures de qualité du code | [docs/IV-qualite-code/questions-conceptuelles.md#q1](./docs/IV-qualite-code/questions-conceptuelles.md#q1) |
| 2 | Types de tests (unitaire, intégration, fonctionnel) | [docs/IV-qualite-code/questions-conceptuelles.md#q2](./docs/IV-qualite-code/questions-conceptuelles.md#q2) |
| 3 | Revue de code | [docs/IV-qualite-code/questions-conceptuelles.md#q3](./docs/IV-qualite-code/questions-conceptuelles.md#q3) |

### Questions basées sur des scénarios

| # | Scénario | Lien |
|---|----------|------|
| 4 | Déboguer une application Symfony lente | [docs/IV-qualite-code/questions-conceptuelles.md#q4](./docs/IV-qualite-code/questions-conceptuelles.md#q4-débogage-dune-application-symfony-lente) |
| 5 | Corriger et déployer un bug en production | [docs/IV-qualite-code/questions-conceptuelles.md#q5](./docs/IV-qualite-code/questions-conceptuelles.md#q5-correction-dun-bug-en-production) |

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

**Réponse**: [docs/V-devops-infrastructure/questions-conceptuelles.md#tâche-déployer-symfony-sur-gcp](./docs/V-devops-infrastructure/questions-conceptuelles.md#tâche-déployer-symfony-sur-gcp)

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
- **PHP 8.4+** ([Installation PHP](https://www.php.net/downloads))
- **Composer** ([Installation Composer](https://getcomposer.org/download/))
- **Docker & Docker Compose** ([Installation Docker](https://docs.docker.com/get-docker/))

### Étapes d'installation

```bash
# 1. Cloner le repository
git clone git@github.com:poupi-devy/test-technique-LEP.git
cd test-technique-LEP/exercices/app

# 2. Installer les dépendances PHP
composer install

# 3. Configurer l'environnement
cp .env.example .env

# 4. Démarrer les services Docker (PostgreSQL)
docker compose up -d

# 5. Attendre que PostgreSQL soit prêt (environ 5-10 secondes)
sleep 10

# 6. Créer la base de données et exécuter les migrations
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate

# Ou utiliser la commande du Makefile
make db

# 7. (Optionnel) Démarrer le serveur PHP
php -S 127.0.0.1:8000 -t public
```

**Accéder à l'application:**
- API: http://localhost:8000/api/v1/
- Tests: `make test` ou `php bin/phpunit`

**Arrêter les services Docker:**
```bash
docker compose down
```

### Tests

#### Suite de tests unitaires (27 tests)

Le projet inclut une suite complète de tests unitaires couvrant les services clés:

```bash
# Lancer tous les tests
make test

# Ou directement avec PHPUnit
php bin/phpunit

# Tests unitaires spécifiques
php bin/phpunit tests/Unit/Service/BookImportHydratorTest.php
php bin/phpunit tests/Unit/Service/ProductHydratorTest.php
php bin/phpunit tests/Unit/Service/BookPersisterTest.php
php bin/phpunit tests/Unit/Service/ValidatorWrapperTest.php
php bin/phpunit tests/Unit/EventHandler/PersistProductHandlerTest.php
php bin/phpunit tests/Unit/Parser/CsvFileParserTest.php
```

**Résultat**: 27 tests passant, 64 assertions, 0 warnings ✅

#### Analyse statique du code

```bash
# PHPStan (level 9 - analyse maximale)
make stan

# Psalm (type checking avancé)
make psalm

# ECS (linting et standards PSR-12)
make ecs

# Rector (refactoring PHP 8.4)
make rector
```

**Résultat**: 0 erreurs PHPStan, 0 erreurs Psalm ✅

### Exécuter les exercices

#### I. Commande d'importation CSV
```bash
php bin/console app:import:books data/books.csv
```

#### II. Requête SQL
Consultez [src/Database/queries.sql](./src/Database/queries.sql)

#### III. Endpoint API
```bash
# Démarrer le serveur PHP
php -S 127.0.0.1:8000 -t public

# Dans un autre terminal, tester l'API
curl -X POST http://localhost:8000/api/v1/products \
  -H "Content-Type: application/json" \
  -d '{"name":"Laptop","price":"999.99","categoryId":1}'
```

---

## Principes du Code

- **Typage fort**: PHP 8.4 avec type hints obligatoires
- **SOLID**: Single Responsibility, Open/Closed, Liskov, Interface Segregation, Dependency Inversion
- **DRY**: Pas de duplication, réutilisabilité maximale
- **Code propre**: Bien organisé, séparation des responsabilités
- **Tests**: Couverture complète (unitaires, fonctionnels, intégration)
- **Constantes et Enums**: Pas de magic numbers

---

## 🚀 Pour un projet professionnel complet

En contexte de production, j'aurais intégré les technologies et patterns suivants:

### API et Documentation

**API Platform** (`api-platform/core`)
- Génération automatique d'API CRUD RESTful/GraphQL
- Gestion des collections, filtrage, pagination, tri
- Documentation OpenAPI/Swagger automatique
- Validation intégrée
- Sérialisation/Normalisation de groupe

### Documentation API

**Nelmio API Doc** (`nelmio/api-doc-bundle`)
- Documentation Swagger/OpenAPI interactive
- Annotations pour décrire les endpoints
- Exemples de requêtes/réponses
- Gestion des codes d'erreur

### Traçabilité des données

**Doctrine Extensions (Gedmo)** (`gedmo/doctrine-extensions`)
- **Timestampable**: `createdAt`, `updatedAt` automatiques
- **Blameable**: Tracking de qui a créé/modifié (userId)
- **SoftDeleteable**: Soft delete (suppression logique)
- **Loggable**: Audit trail complet avec versions
- Impact: Traçabilité RGPD-compliant, récupération d'historique

### Sérialisation et Normalisation

**Serializer Component** (natif Symfony)
- **Serialization Groups**: Contrôle des champs exposés par contexte
- Exemple: `#[Groups(['product:read', 'product:write'])]`
- Relations imbriquées avec contrôle granulaire
- DTO différents par contexte (API publique vs admin)

### Cache

**Redis** via `symfony/cache`
- Cache HTTP avec tags
- Cache applicatif pour requêtes coûteuses
- Query result caching
- Rate limiting distributed
- Session sharing

Exemple:
```php
#[Cache(ttl: 3600, tags: ['products'])]
public function getProduct(int $id): Product
```

### Autres améliorations professionnelles

**Authentification/Autorisation**
- JWT avec `lexik/jwt-authentication-bundle`
- API Key management
- OAuth2 via `trikoder/oauth2-bundle`
- RBAC (Role-Based Access Control)

**Validation avancée**
- Custom validators Symfony
- Business rule validation
- Domain validation au niveau service

**Monitoring & Observabilité**
- ELK Stack (Elasticsearch, Logstash, Kibana)
- Sentry pour error tracking
- Datadog/New Relic pour APM
- Prometheus pour métriques

**Async & Jobs**
- Message Queue (RabbitMQ/Redis)
- `symfony/messenger` pour async processing
- Scheduled jobs avec `symfony/scheduler`

**Rate Limiting**
- `symfony/rate-limiter`
- Distribué avec Redis
- Par IP / API Key / User

---

## Notes sur cette implémentation

Cet exercice de recrutement se concentre sur les **fondamentaux et bonnes pratiques**. Le code fourni démontre:
- Architecture solide et extensible
- Respect des principes SOLID et DRY
- Séparation claire des responsabilités
- Event-driven patterns
- Validation et gestion d'erreurs robustes

---