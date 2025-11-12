<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Create tables for exercises 1 and 3
 *
 * - Exercice 1: books table for CSV import command
 * - Exercice 3: products table for REST API
 */
final class Version20241111164000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create books and products tables for exercises 1 and 3';
    }

    public function up(Schema $schema): void
    {
        // Create books table (Exercice 1: ImportBooksCommand)
        $this->addSql('CREATE TABLE books (
            id SERIAL PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            author VARCHAR(255) NOT NULL,
            year INTEGER NOT NULL CHECK (year >= 1000),
            isbn VARCHAR(20) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )');

        $this->addSql('CREATE INDEX idx_books_isbn ON books(isbn)');
        $this->addSql('CREATE INDEX idx_books_author ON books(author)');
        $this->addSql('CREATE INDEX idx_books_created_at ON books(created_at)');

        // Create products table (Exercice 3: POST /api/v1/products)
        $this->addSql('CREATE TABLE products (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description VARCHAR(5000),
            price NUMERIC(10, 2) NOT NULL CHECK (price > 0),
            category_id INTEGER NOT NULL CHECK (category_id > 0),
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        )');

        $this->addSql('CREATE INDEX idx_products_category_id ON products(category_id)');
        $this->addSql('CREATE INDEX idx_products_created_at ON products(created_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS products');
        $this->addSql('DROP TABLE IF EXISTS books');
    }
}