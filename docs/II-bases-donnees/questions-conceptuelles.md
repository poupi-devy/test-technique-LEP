# II. Connaissance des Bases de Données - Questions Conceptuelles

**[← Retour au README](../../README.md)**

---

## Q1. Bases de Données Relationnelles vs Non-Relationnelles

### Relationnelles (SQL)

**Caractéristiques**:
- Structure en tables avec lignes et colonnes
- Schéma fixe défini à l'avance
- Relations entre tables via clés étrangères
- Requêtes SQL standardisées

**Avantages**:
- **Intégrité des données**: Contraintes et relations assurées
- **Flexibilité des requêtes**: SQL puissant et standardisé
- **ACID**: Garantit la cohérence et la fiabilité
- **Normalisées**: Réduction de la redondance

**Inconvénients**:
- **Scalabilité limitée**: Difficile à scaler horizontalement
- **Rigidité du schéma**: Migrations coûteuses
- **Performance sur données massives**: Requêtes complexes peuvent être lentes

### Non-Relationnelles (NoSQL)

**Caractéristiques**:
- Structures flexibles (documents, clé-valeur, graphes, colonnes)
- Schéma dynamique ou absent
- Pas de relations formelles
- Requêtes spécifiques au type de base

**Avantages**:
- **Scalabilité horizontale**: Distribution facile sur plusieurs serveurs
- **Flexibilité du schéma**: Adaptation rapide aux changements
- **Performance sur données massives**: Optimisées pour gros volumes
- **Haute disponibilité**: Réplication et partitionnement natifs

**Inconvénients**:
- **Cohérence variable**: BASE au lieu d'ACID (dans certains cas)
- **Requêtes complexes difficiles**: Pas de jointures sophistiquées
- **Redondance des données**: Dénormalisation nécessaire
- **Moins standardisé**: Chaque technologie a ses propres règles

### Quand Utiliser Quoi?

- **Relationnelles**: Données structurées, relations complexes, intégrité critique (ERP, comptabilité)
- **Non-Relationnelles**: Données volumineuses, flexibles, scalabilité requise (logs, cache, catalogs produits)

---

## Q2. Formes de Normalisation (1NF, 2NF, 3NF)

La normalisation est un processus structurant une base de données pour **éliminer la redondance** et **assurer l'intégrité des données**.

### Première Forme Normale (1NF)

**Règle**: Chaque colonne contient des valeurs atomiques (indivisibles).

**Critères**:
- Pas de colonnes avec plusieurs valeurs (listes, tableaux)
- Pas de lignes dupliquées
- Chaque attribut a un domaine unique

### Deuxième Forme Normale (2NF)

**Règle**: La table est en 1NF ET chaque attribut non-clé dépend entièrement de la clé primaire.

**Critères**:
- Éliminer les **dépendances partielles**
- Chaque colonne dépend de l'intégralité de la clé primaire
- Requiert une clé primaire

### Troisième Forme Normale (3NF)

**Règle**: La table est en 2NF ET aucun attribut non-clé ne dépend d'un autre attribut non-clé.

**Critères**:
- Éliminer les **dépendances transitives**
- Les attributs ne dépendent que de la clé primaire, pas les uns des autres

**Note**: Le coût en complexité dépasse généralement les bénéfices pour la plupart des applications.

---

## Q3. Index dans une Base de Données

### Concept

Un index est une **structure de données optimisée pour accélérer la recherche** de lignes basée sur les valeurs d'une ou plusieurs colonnes. Similaire à un index dans un livre.

### Types d'Index

**Index Simple**: Une seule colonne
**Index Composé**: Plusieurs colonnes
**Index Unique**: Garantit l'unicité des valeurs
**Index Texte Intégral**: Pour les recherches de texte

### Amélioration des Performances

L'index permet au moteur de base de données de:
- **Localiser rapidement** les données sans parcourir la table entière (O(log n) au lieu de O(n))
- **Trier efficacement** les résultats
- **Optimiser les jointures** entre tables

### Compromis et Coûts

**Inconvénients**:
- **Espace disque supplémentaire**: L'index duplique les données
- **Ralentissement des écritures**: INSERT, UPDATE, DELETE doivent mettre à jour l'index
- **Coût de maintenance**: Réorganisation périodique de l'index
- **Surcharge mémoire**: L'index doit rester en mémoire pour performance

### Stratégie d'Indexation

- Index sur colonnes fréquemment recherchées ou triées
- Index sur colonnes de jointure
- Éviter trop d'index (augmente le coût des écritures)
- Monitorer l'utilisation des index

---

## Q4. Transactions de Base de Données

### Concept

Une transaction est une **unité de travail atomique** qui exécute une ou plusieurs opérations SQL de manière indivisible.

### Propriétés ACID

| Propriété | Définition |
|-----------|-----------|
| **A - Atomicité** | La transaction est tout ou rien. Soit elle s'exécute complètement, soit elle est annulée entièrement. |
| **C - Cohérence** | La base passe d'un état cohérent à un autre. Les contraintes d'intégrité sont respectées. |
| **I - Isolation** | Les transactions concurrentes n'interfèrent pas les unes avec les autres. |
| **D - Durabilité** | Une fois validée (commit), la transaction est permanente même en cas de panne. |

### Garanties de Cohérence et d'Intégrité

**Atomicité**:
- Empêche les modifications partielles
- Exemple: Un transfert bancaire crédite un compte ET débite l'autre, ou ni l'un ni l'autre

**Isolation**:
- Niveaux d'isolation (READ UNCOMMITTED, READ COMMITTED, REPEATABLE READ, SERIALIZABLE)
- Prévient les anomalies: lectures fantômes, lectures sales, lectures non-répétables

**Cohérence**:
- Contraintes d'intégrité référentielle
- Déclenche les rollback si violation détectée

**Durabilité**:
- Écriture sur disque après commit
- Logs de transactions pour récupération après panne

### Contrôle de Concurrence

- **Verrous (Locking)**: Empêche les modifications simultanées
- **Versioning**: Plusieurs versions des données (MVCC - Multi-Version Concurrency Control)

---

## Q5. Techniques Courantes d'Optimisation

### Optimisation des Requêtes

**1. Indexation**
- Index sur colonnes de filtrage, jointure et tri
- Analyser les plans d'exécution

**2. Réécriture de Requêtes**
- Éviter les sous-requêtes corelées
- Utiliser les jointures plutôt que les boucles
- Sélectionner uniquement les colonnes nécessaires (pas SELECT *)

**3. Dénormalisation Ciblée**
- Ajouter des champs calculés pour éviter les agrégations
- Acceptable quand la performance est critique

### Optimisation au Niveau Physique

**4. Partitionnement**
- Diviser une grande table en sous-tables
- Améliore la performance sur données volumineuses
- Partitionnement par plage (date), liste (région) ou hash

**5. Cache**
- Cache au niveau application (Redis, Memcached)
- Réduit les requêtes répétitives
- Invalidation sélective nécessaire

### Optimisation au Niveau Conception

**6. Schéma Optimal**
- Normalisation équilibrée
- Types de données appropriés
- Clés primaires efficaces

**7. Monitoring et Analyse**
- Logs des requêtes lentes
- Plans d'exécution
- Statistiques de base de données

---

## Navigation

**[← Retour au README](../../README.md)** | **[Exercice Pratique →](../../exercices/02-requete-sql/README.md)**