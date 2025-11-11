# Exercice Pratique: Requête SQL Multi-Table

**[← Retour au README](../../README.md)**

---

## Scénario

Deux tables PostgreSQL:

```sql
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255)
);

CREATE TABLE orders (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id),
    order_date DATE,
    total_amount DECIMAL(10, 2)
);
```

**Tâche**: Récupérer le nom et l'email de tous les utilisateurs qui ont passé au moins une commande avec un montant total supérieur à 100. Trier par nom d'utilisateur croissant.

---

## Solutions

### Version 1 (Recommandée): Common Table Expression (CTE)

```sql
WITH orders_above_100 AS (
    SELECT DISTINCT user_id
    FROM orders
    WHERE total_amount > 100
)
SELECT u.name, u.email
FROM users u
JOIN orders_above_100 oa ON u.id = oa.user_id
ORDER BY u.name ASC;
```

**Approche**:
- Isoler la logique de filtrage dans une CTE
- Clarté de la requête principale
- Facilite la maintenance

**Avantages**:
- **Meilleure lisibilité**: Logique clairement séparée en haut
- **Maintenabilité**: Facile à modifier et à comprendre
- **Évolutivité**: Réutilisable dans la même requête
- **Documentation**: Le nom de la CTE documente l'intention

---

### Version 2: JOIN avec DISTINCT

```sql
SELECT DISTINCT u.name, u.email
FROM users u
JOIN orders o ON u.id = o.user_id
WHERE o.total_amount > 100
ORDER BY u.name ASC;
```

**Approche**:
- Jointure directe entre les tables
- Filtrage sur le montant
- DISTINCT pour éviter les doublons

**Avantages**:
- Simplicité pour requêtes petites/moyennes
- Performance optimale pour tables < 100k lignes
- Syntaxe directe et compacte

**Cas d'usage**: Données petites à moyennes avec logique simple

---

### Version 3: EXISTS (Haute Performance)

```sql
SELECT DISTINCT u.name, u.email
FROM users u
WHERE EXISTS (
    SELECT 1
    FROM orders o
    WHERE o.user_id = u.id
    AND o.total_amount > 100
)
ORDER BY u.name ASC;
```

**Approche**:
- Vérification d'existence plutôt que récupération de liste
- Arrêt après la première correspondance

**Avantages**:
- **Performance supérieure** sur très grandes tables
- Économe en mémoire
- Scalable pour millions de lignes

**Cas d'usage**: Tables massives (millions de lignes), haute disponibilité requise

---

### Version 4: GROUP BY avec HAVING

```sql
SELECT u.name, u.email
FROM users u
JOIN orders o ON u.id = o.user_id
WHERE o.total_amount > 100
GROUP BY u.id, u.name, u.email
ORDER BY u.name ASC;
```

**Approche**:
- Groupement par utilisateur
- GROUP BY garantit l'unicité sans DISTINCT

**Avantages**:
- Explicite sur l'agrégation
- Extensible pour ajout de calculs (COUNT, SUM, etc.)

---

### Version 5: Sous-requête dans WHERE

```sql
SELECT u.name, u.email
FROM users u
WHERE u.id IN (
    SELECT DISTINCT user_id
    FROM orders
    WHERE total_amount > 100
)
ORDER BY u.name ASC;
```

**Approche**:
- Sous-requête pour identifier les utilisateurs qualifiés

**Inconvénients**:
- Moins optimal sur grandes tables
- Optimiseur moderne convertit généralement en JOIN

---

## Recommandations: Choisir Selon la Taille des Tables

### Priorité: Maintenabilité et Lisibilité

Le choix de la requête dépend avant tout de la taille de vos données, mais **la maintenabilité et la lisibilité du code doivent être prioritaires** car:
- Votre équipe devra comprendre et modifier la requête
- Une requête lisible réduit les bugs et les temps de correction
- La performance peut être améliorée par des index sans réécrire la requête

### Petit Volume (< 100k lignes)

**Version 1 - CTE (Recommandée)**
- Maintenabilité: ⭐⭐⭐⭐⭐ Maximum
- Performance: Excellente
- Lisibilité: Optimale grâce à la séparation claire

**Alternative acceptable**: Version 2 (JOIN + DISTINCT) si requête très simple

### Volume Moyen (100k - 10M lignes)

**Version 1 - CTE (Toujours recommandée)**
- Restez avec les CTEs pour la maintenabilité
- Les index feront toute la différence de performance
- La requête reste claire et évolutive

### Grand Volume (> 10M lignes)

**Version 3 - EXISTS**
- Nécessaire pour performance optimale
- Scalable et économe en ressources
- Réécrire uniquement si les index sur CTE ne suffisent pas

---

## Principe Fondamental

**"Écrivez pour un humain, l'optimiseur s'occupe du reste"**

- Privilégiez les CTEs pour leur clarté structurelle
- Optimisez via les index, pas via la syntaxe
- Mesurez avant de refactoriser (EXPLAIN ANALYZE)

---

## Index Recommandés

Pour optimiser ces requêtes:

```sql
CREATE INDEX idx_orders_user_id ON orders(user_id);
CREATE INDEX idx_orders_amount ON orders(total_amount);
CREATE INDEX idx_orders_user_amount ON orders(user_id, total_amount);
```

L'index composé `(user_id, total_amount)` est particulièrement utile pour les versions 1, 2, 3.

---

## Navigation

**[← Retour au README](../../README.md)**