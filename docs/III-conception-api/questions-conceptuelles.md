# III. Conception et Développement d'API - Questions Conceptuelles

**[← Retour au README](../../README.md)**

---

## Q1. Principes Clés de la Conception RESTful

Une API RESTful repose sur des principes architecturaux garantissant simplicité, cohérence et évolutivité.

| Principe | Description | Exemple |
|----------|-------------|---------|
| **Ressources identifiables par URI** | Chaque ressource a une URL unique | `/api/v1/products/42` |
| **Méthodes HTTP standards** | GET, POST, PUT, DELETE selon l'action | Voir Q2 |
| **Stateless** | Chaque requête contient toutes les infos nécessaires (pas de session serveur) | Token à chaque requête |
| **Représentations multiples** | JSON, XML, YAML selon header Accept | `Accept: application/json` |
| **Codes HTTP cohérents** | Statuts précis pour informer du résultat | 200 OK, 201 Created, 404 Not Found |
| **HATEOAS** | L'API guide le client via des liens (optionnel) | `_links: { "self": "/users/1" }` |

---

## Q2. Méthodes HTTP: GET, POST, PUT, DELETE

### GET

**Objectif**: Récupérer une ressource

**Caractéristiques**:
- Sûre (lecture seule)
- Idempotente (même résultat si répétée)
- Pas de corps de requête
- Cacheable

**Exemple**: `GET /products/123`

---

### POST

**Objectif**: Créer une nouvelle ressource

**Caractéristiques**:
- Pas sûre (modifie l'état serveur)
- Non-idempotente (plusieurs appels = plusieurs créations)
- Corps de requête requis
- Non-cacheable par défaut

**Exemple**: `POST /products` avec payload

**Codes de réponse**: 201 (Created), 400 (Bad Request), 422 (Unprocessable Entity)

---

### PUT

**Objectif**: Remplacer complètement une ressource

**Caractéristiques**:
- Pas sûre
- Idempotente (même résultat si répétée)
- Corps de requête requis
- Nécessite l'ID de la ressource

**Exemple**: `PUT /products/123` (remplace le produit 123 entièrement)

**Codes de réponse**: 200 (OK), 201 (Created si créée), 204 (No Content)

---

### DELETE

**Objectif**: Supprimer une ressource

**Caractéristiques**:
- Pas sûre
- Idempotente (supprimer deux fois = supprimé)
- Pas de corps de requête
- Nécessite l'ID de la ressource

**Exemple**: `DELETE /products/123`

**Codes de réponse**: 200, 204 (No Content), 404 (Not Found)

---

### PATCH (Variante)

**Objectif**: Mise à jour partielle

**Caractéristiques**:
- Pas sûre
- Non-idempotente (dépend de l'ordre d'exécution)
- Corps de requête requis
- Modifie seulement les champs spécifiés

**Exemple**: `PATCH /products/123` avec `{"price": 99.99}`

---

## Q3. Versionnage d'API

### Avantages du Versionnage

- **Rétrocompatibilité**: Anciennes versions restent fonctionnelles
- **Évolution sûre**: Changements majeurs sans casser les clients
- **Migration progressive**: Les clients migrent à leur rythme
- **Maintenance**: Supprimer anciennes versions planifiées

### Stratégies d'Implémentation

**1. Versioning par URL**
```
GET /api/v1/products
GET /api/v2/products
```

Avantages: Explicite, facile à comprendre
Inconvénients: Redondance, maintenance multiple

**2. Versioning par Header**
```
GET /api/products
Header: Accept-Version: 1.0
```

Avantages: URL unique, versioning transparent
Inconvénients: Moins visible

**3. Versioning par Query String**
```
GET /api/products?version=1
```

Avantages: Simple
Inconvénients: Moins conventionnel

---

## Q4. Authentification et Sécurisation

### Clés API

**Concept**: Token statique associé au client

**Avantages**:
- Simplicité
- Faible overhead
- Serveur-less friendly

**Inconvénients**:
- Pas de contexte utilisateur
- Difficile à révoquer rapidement
- Révolution nécessaire si compromise

**Cas d'usage**: Accès machine-to-machine, services internes

---

### OAuth 2.0

**Concept**: Délégation d'accès via autorité tierce

**Flux**:
1. Utilisateur s'authentifie auprès d'une autorité (Google, GitHub)
2. Autorité émet token d'accès
3. Client utilise token pour accéder à API

**Avantages**:
- Pas de stockage de mots de passe chez le fournisseur
- Révocation rapide possible
- Contexte riche (scopes)
- Standard industriel

**Inconvénients**:
- Complexité d'implémentation
- Dépendance tierce
- Configuration nécessaire

**Cas d'usage**: Authentification utilisateur, SSO, applications tierces

---

### JWT (JSON Web Tokens)

**Concept**: Token auto-contenu contenant claims (données)

**Structure**: `header.payload.signature`

**Avantages**:
- Stateless (serveur n'a pas besoin de stocker tokens)
- Self-contained (claims inclus dans le token)
- Scalabilité horizontale facile
- Multiplateforme

**Inconvénients**:
- Révocation difficile (token valide jusqu'à expiration)
- Taille plus grande que sessions
- Secret partagé ou clés asymétriques nécessaires

**Cas d'usage**: APIs mobiles, single-page applications, microservices

---

### Tableau Comparatif

| Méthode | Complexité | Scalabilité | Révocation | Cas d'Usage |
|---------|-----------|-------------|-----------|------------|
| **Clés API** | Basse | Moyenne | Lente | Machine-to-machine |
| **OAuth 2.0** | Haute | Élevée | Rapide | Utilisateurs, SSO |
| **JWT** | Moyenne | Élevée | Difficile | APIs modernes |

---

## Q5. Rate Limiting (Limitation du Débit)

### Définition et Objectifs

Empêcher un client d'effectuer trop de requêtes dans un laps de temps donné pour protéger l'API contre les abus, les attaques DoS et préserver les ressources.

**Exemple**: Un utilisateur ne peut pas faire plus de 100 requêtes par minute.

### Pourquoi C'est Important

- **Prévention d'abus**: Protection contre DoS et scraping massif
- **Équité des ressources**: Chaque client dispose de sa part
- **Protection économique**: Limiter les coûts serveur/API tierce
- **Performance**: Système stable pour tous les clients

### Méthodes d'Implémentation

| Méthode | Description | Outils / Exemples |
|---------|-------------|-------------------|
| **Token Bucket** | Chaque requête consomme un jeton. Recharge régulière. | Redis, algorithms classiques |
| **Sliding Window** | Comptage dans fenêtre mobile précis. | Coût computationnel modéré |
| **Middleware Symfony** | Vérification par IP ou token avant traitement | `symfony/rate-limiter` |
| **Reverse Proxy** | Limitation avant d'atteindre l'app | Nginx, Cloudflare, AWS API Gateway |

### Headers de Réponse Standard

```
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 75
X-RateLimit-Reset: 1234567890
X-RateLimit-Retry-After: 45
```

**Code HTTP**: `429 Too Many Requests` quand dépassé

---

## Navigation

**[← Section II](../II-bases-donnees/questions-conceptuelles.md)** | **[← Retour au README](../../README.md)** | **[Exercice Pratique →](../../exercices/03-endpoint-api/README.md)**