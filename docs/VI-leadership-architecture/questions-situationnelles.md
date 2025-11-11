# VI. Leadership et Architecture - Questions Situationnelles

**[← Retour au README](../../README.md)**

---

## Q1. Monolithe vs Microservices

### Critères Comparatifs

| Critère | Monolithique | Microservices |
|---------|-------------|---------------|
| **Structure** | Application unique | Services indépendants |
| **Déploiement** | Unique (tout ou rien) | Par service |
| **Complexité** | Faible au début | Haute (communication, orchestration) |
| **Scalabilité** | Globale | Par composant |
| **Maintenance** | Facile initialement | Plus d'overhead |

### Décision

**Dépend principalement**:
- **Taille équipe**: Petite → monolithe. Grande (10+) → microservices
- **Volume trafic**: Faible → monolithe. Fort et spikes → microservices
- **Fréquence déploiement**: Rare → monolithe. Quotidien → microservices

### Stratégie

1. **Commencer monolithique** (startup, MVP)
2. **Migrer vers microservices** si complexité/scalabilité l'exigent
3. Analyser métriques avant de décider

### Cas d'Usage

- **Monolithe**: Startups, MVP, petits SI
- **Microservices**: Grandes entreprises, besoin scalabilité horizontale

---

## Q2. Développeur Produisant Code de Mauvaise Qualité

### Approche Bienveillante et Structurée

#### 1. Observer & Documenter
- Collecter exemples concrets (tests manquants, patterns antipattern)
- Pas d'accusation, des faits

#### 2. Entretien Individuel
- Comprendre la cause:
  - Surcharge de travail?
  - Manque de connaissance?
  - Démotivation?
  - Comprendre l'équipe?
  - Mauvaise definition des taches?

#### 3. Plan d'Accompagnement
- **Pair programming** sur features clés
- **Revue de code** constructive et bienveillante
- **Formations** ciblées (tests, patterns, architecture)
- **Suivi régulier** avec objectifs mesurables

#### 4. Valorisation
- Reconnaître progrès
- Célébrer améliorations
- Maintenir motivation

---

## Q3. Désaccord Architectural dans l'Équipe

### Rôle du Leader: Facilitateur (Pas Arbitre)

#### 1. Écouter
- Chaque point de vue sans interruption
- Comprendre les fears & concerns

#### 2. Critères Objectifs
- Performance requise?
- Maintenabilité?
- Coût infrastructure?
- Time-to-market?

#### 3. Comparer sur Faits
- Pas d'opinions, des données
- POC rapide si incertain
- Expérimentation contrôlée

#### 4. Décider & Documenter
- Tracer la décision via **ADR** (Architecture Decision Record)

---

## Q4. Dernier Défi Technique

### Exemple de Réponse

**Défi**: Concevoir architecture asynchrone robuste pour ecommerce complexe

**Contexte**:
- Intégration Sylius + Odoo + services externes (StoreTicket, MCC, etc.)
- Besoin: Traiter commandes en parallèle, livraison instantanée produits numériques

**Approche**:
- Symfony Messenger pour orchestration asynchrone
- Workers AWS Lambda (scalabilité serverless)
- Retry logic + exponential backoff
- Logs centralisés (CloudWatch/ELK)

---

## Navigation

**[← Section V](../V-devops-infrastructure/questions-conceptuelles.md)** | **[← Retour au README](../../README.md)**