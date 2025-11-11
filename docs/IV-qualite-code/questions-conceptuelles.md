# IV. Qualité du Code et Bonnes Pratiques - Questions Conceptuelles

**[← Retour au README](../../README.md)**

---

## Q1. Mesures de la Qualité du Code

### Outils de Suivi

**Analyse Statique**:
- **PHPStan**: Typage statique, analyse approfondie PHP
- **PHP_CodeSniffer**: Standards de codage (PSR-12)
- **Psalm**: Type checker, sécurité
- **Rector**: Automated refactoring et code quality improvement

**Couverture de Tests**:
- **PHPUnit**: Framework de test + coverage report

---

## Q2. Types de Tests

### Tests Unitaires

**Objectif**: Tester une fonction/méthode isolée

**Caractéristiques**:
- Rapides
- Pas de dépendances externes
- Faciles à débugger

**Avantages**:
- Feedback immédiat
- Couverture granulaire
- Régression détectée rapidement

---

### Tests d'Intégration

**Objectif**: Tester l'interaction entre plusieurs composants (DB, cache, services)

**Caractéristiques**:
- Plus lents que unitaires
- Requièrent setup (base de données)
- Testent le wiring réel

**Avantages**:
- Détectent erreurs de configuration
- Valident interactions réelles
- Couvrent bugs liés à l'intégration

---

### Tests Fonctionnels

**Objectif**: Tester un flux utilisateur complet (API endpoint, page web)

**Caractéristiques**:
- Simuler comportement utilisateur
- Tester requête HTTP → Réponse
- Vérifier codes HTTP, headers, contenu

**Avantages**:
- Valident specs métier
- Détectent regressions haut-niveau
- Confiance pour production

---

## Q3. Revue de Code

### Définition

Processus collaboratif où un développeur examine le code d'un collègue avant merge. L'objectif est d'améliorer la qualité globale du projet **en bienveillance** et avec une **responsabilité collective**.

### Avantages Clés

**Détection Précoce**
- Identifier bugs et incohérences avant production
- Éviter dépenses d'énergie en corrections tardives

**Partage de Connaissance**
- Éliminer les zones d'ombre dans le codebase
- Chaque dev comprend les décisions techniques globales
- Formation continue collective (montée en compétence)

**Cohérence Technique**
- Mêmes pratiques partout dans le projet
- Évite la fragmentation et les patterns conflictuels
- Code plus prévisible pour tous

**Lisibilité et Maintenabilité**
- Code mieux documenté et structuré
- Onboarding facilité pour nouvelles personnes
- Moins de dettes techniques

**Bénéfice Mutuels**
- **Reviewer**: Apprendre des solutions d'autres, renforcer expertise
- **Auteur**: Recevoir feedback constructif, progresser

### Approche Bienveillante et Constructive

Pour éviter la sur-qualité ou les critiques démotivantes:

- **Respecter les Guidelines**: Avoir des critères clairs (définis ensemble)
- **Dialoguer, pas juger**: Questions plutôt que critiques
- **Reconnaître les bonnes pratiques**: Valoriser les efforts
- **Accepter les différences**: Plusieurs solutions valides peuvent coexister
- **Focus sur l'impact**: Priorité aux bugs, sécurité, lisibilité; flexibilité sur style

### Checklist de Revue Équilibrée

- ✓ Logique correcte et cohérente
- ✓ Tests couvrent le code nouveau
- ✓ Standards de codage (PSR-12) respectés
- ✓ Pas de secrets/credentials exposés
- ✓ Documentation si nécessaire
- ~ Style/noms: suggérer si vraiment utile

---

## Q4. Débogage d'une Application Symfony Lente

### Plan d'Action

**1. Profiling Initial**

Collecter les données de performance:
- Symfony Debug Toolbar (développement)
- Blackfire (production-safe)
- Sentry (error tracking + performance monitoring)
- New Relic (APM complet)

**2. Identifier le Goulot**

- Requêtes DB: N+1 queries, manque d'index
- Template Rendering: Boucles complexes
- Code PHP: Algorithmes inefficaces (O(n²))
- Cache absent ou expiré

**3. Analyser les Logs**

Vérifier Symfony logs et error tracking (Bugsnag, Sentry) pour:
- Erreurs et warnings
- Stack traces
- Patterns récurrents

**4. Appliquer Corrections**

| Goulot | Solution |
|--------|----------|
| N+1 queries | Eager loading, jointures |
| Pas d'index | Ajouter INDEX WHERE/JOIN |
| Cache absent | Redis, Memcached |
| Algorithme coûteux | Optimiser logique |

---

## Q5. Correction d'un Bug en Production

### Étapes du Processus

#### 1. Reproduction

- Collecter exact steps pour reproduire
- Récupérer logs (stack trace, error logs)
- Reproduire sur environnement similaire

#### 2. Créer Branche Hotfix

```bash
git checkout -b hotfix/ISSUE-123-description-courte
```

#### 3. Écrire Test Reproduisant le Bug

Créer test qui échoue avant correction, passe après:
```php
public function testBugIsFixed() {
    // Échoue avant fix, passe après
}
```

#### 4. Implémenter la Correction

- Correction minimale (scope limité)
- Suivre conventions existantes
- Commenter si complexe

### Vérification Automatique via CI/CD

La branche hotfix passe obligatoirement par la CI/CD avant merge:
- Tests: PHPUnit passe
- Analyse statique: PHPStan, Psalm validé
- Standards: PHP_CodeSniffer conforme
- Coverage: Seuil minimum atteint

**Aucune vérification manuelle de code quality** - tout est automatisé et obligatoire.


---

## Navigation

**[← Section III](../III-conception-api/questions-conceptuelles.md)** | **[← Retour au README](../../README.md)** | **[Section V →](../V-devops-infrastructure/questions-conceptuelles.md)**