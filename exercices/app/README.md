# Exercices Pratiques - Symfony 7.3

Projet Symfony light pour démontrer les exercices 1 et 3 du test technique.

## Installation

```bash
cd exercices/app
composer install
```

## Configuration Environnement

Copier `.env` et configurer la base de données :

```bash
cp .env .env.local
```

Éditer `.env.local` pour configurer `DATABASE_URL` (PostgreSQL 12+) :

```
DATABASE_URL="postgresql://user:password@127.0.0.1:5432/exercices_db"
```

## Création Base de Données

```bash
# Créer la base
php bin/console doctrine:database:create

# Exécuter les migrations
php bin/console doctrine:migrations:migrate
```

### Migrations

La migration `Version20241111164000.php` crée :

**Table `books`** (Exercice 1):
- `id` (PRIMARY KEY, SERIAL)
- `title` (VARCHAR 255) - Titre du livre
- `author` (VARCHAR 255) - Auteur
- `year` (INTEGER) - Année avec validation (1000-2025)
- `isbn` (VARCHAR 20 UNIQUE) - ISBN unique
- `created_at` (TIMESTAMP DEFAULT CURRENT_TIMESTAMP)

Indexes:
- `idx_books_isbn` - Recherche rapide par ISBN
- `idx_books_author` - Recherche rapide par auteur

**Table `products`** (Exercice 3):
- `id` (PRIMARY KEY, SERIAL)
- `name` (VARCHAR 255) - Nom du produit
- `description` (VARCHAR 5000, nullable) - Description
- `price` (NUMERIC 10,2) - Prix avec validation (> 0)
- `category_id` (INTEGER) - ID catégorie avec validation (> 0)
- `created_at` (TIMESTAMP DEFAULT CURRENT_TIMESTAMP)

Indexes:
- `idx_products_category_id` - Recherche par catégorie
- `idx_products_created_at` - Tri par date création

## Exercice 1 : Commande d'Import CSV

### Description

Commande Symfony qui importe des livres à partir d'un fichier CSV.

**Event-Driven**: Chaque ligne du CSV déclenche un événement `BookImportedEvent` qui valide et persiste le livre.

### Utilisation

```bash
php bin/console app:import:books data/books.csv
```

### Structure

- **Entity**: `Book.php` - Entité avec validations (titre, auteur, année, ISBN)
- **Command**: `ImportBooksCommand.php` - Commande pour lire et traiter le CSV
- **Event**: `BookImportedEvent.php` - Événement déclenché par ligne
- **EventHandler**: `ValidateAndPersistBookHandler.php` - Valide et persiste

### Résultat Attendu

```
 ✓ Imported: The Pragmatic Programmer
 ✓ Imported: Clean Code
 ✓ Imported: Design Patterns
 ...

 [OK] Import complete: 5 imported, 0 errors
```

---

## Exercice 3 : Endpoint API RESTful

### Description

Endpoint POST `/api/v1/products` pour créer un produit avec validation.

**Event-Driven**: La création déclenche `ProductCreatedEvent` pour la persistance.

### Utilisation

#### Démarrer le serveur

```bash
symfony server:start
# ou
php -S 127.0.0.1:8000 -t public
```

#### Créer un produit

```bash
curl -X POST http://127.0.0.1:8000/api/v1/products \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Laptop Pro 15",
    "description": "High-performance laptop",
    "price": 1299.99,
    "categoryId": 1
  }'
```

### Réponse Succès (201 Created)

```json
{
  "id": 1,
  "name": "Laptop Pro 15",
  "description": "High-performance laptop",
  "price": 1299.99,
  "categoryId": 1,
  "createdAt": "2024-11-11T16:30:00+00:00"
}
```

Header: `Location: /api/v1/products/1`

### Erreurs Gérées

#### 400 Bad Request - JSON invalide

```bash
curl -X POST http://127.0.0.1:8000/api/v1/products \
  -H "Content-Type: application/json" \
  -d 'invalid json'
```

Response:
```json
{
  "error": "invalid_request",
  "message": "Invalid JSON payload"
}
```

#### 422 Unprocessable Entity - Validation échouée

```bash
curl -X POST http://127.0.0.1:8000/api/v1/products \
  -H "Content-Type: application/json" \
  -d '{
    "name": "X",
    "price": -10,
    "categoryId": 0
  }'
```

Response:
```json
{
  "error": "validation_failed",
  "violations": [
    {
      "field": "name",
      "message": "This value is too short..."
    },
    {
      "field": "price",
      "message": "This value should be greater than 0..."
    }
  ]
}
```

### Structure

- **Entity**: `Product.php` - Entité avec validations
- **Controller**: `ProductController.php` - Endpoint POST
- **Event**: `ProductCreatedEvent.php` - Événement de création
- **EventHandler**: `PersistProductHandler.php` - Persiste le produit

---

## Requêtes SQL (Exercice 2)

Voir [../../02-requete-sql/README.md](../../02-requete-sql/README.md)

---

## Tests

```bash
# Tous les tests
php bin/phpunit

# Tests de la commande
php bin/phpunit tests/Command/

# Tests du contrôleur
php bin/phpunit tests/Controller/
```

---

## Pattern Event-Driven

Le projet utilise le composant Symfony EventDispatcher pour découpler la logique :

1. **Command/Controller** → Déclenche un **Event**
2. **EventHandler** → Écoute et traite l'événement
3. **Entity** → Validée par les contraintes Symfony

Avantages:
- Testabilité (mocker les handlers)
- Réutilisabilité (plusieurs handlers sur un même événement)
- Découplage (commande ≠ persistance)

---

## Prérequis

- PHP 8.4+
- Symfony 7.3
- PostgreSQL 12+
- Composer