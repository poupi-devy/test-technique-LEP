# V. DevOps et Infrastructure - Questions Conceptuelles

**[← Retour au README](../../README.md)**

---

## Q1. Avantages de Docker

### Consistency
- Même environnement dev/test/production
- Élimine "works on my machine" syndrome

### Isolation & Scalabilité
- Conteneurs isolés, déploiement rapide
- Load balancing simple

### Efficacité
- Léger (partage kernel vs VMs)
- Startup rapide (secondes)

### Workflow
- Onboarding: `docker-compose up`
- CI/CD simplifié

---

## Q2. Git et GitHub

### Git
- Version control avec historique complet
- Branches pour isolation features

### GitHub
- Repository centralisé
- Pull Requests pour code review
- Issues & Actions (CI/CD)

### Flux de Travail Courants

**Trunk-Based** (recommandé):
- Branches courtes (1-2 jours)
- Main toujours deployable
- Merge rapide + CI/CD strict

**Git Flow**:
- main (prod) ← release ← develop ← features
- Plus de structure pour gros projets

**GitHub Flow**:
- main ← feature PR → merge
- Simple, moderne

### Bonnes Pratiques
- Commits atomiques, messages clairs
- Branches nommées (`feature/auth`, `bugfix/payment`)
- Code review avant merge
- Pas de force push sur main

---

## Q3. CI/CD

### Concept

**CI (Continuous Integration)**
- Tests automatiques à chaque push
- Build automatisés

**CD (Continuous Deployment/Delivery)**
- Delivery: Builds prêts à déployer (manuel)
- Deployment: Déploiement automatique

### Pipeline
```
Push → Build → Tests → Analysis → Deploy
```

### Avantages
- Détection précoce des erreurs
- Livraison rapide et fréquente
- Qualité automatisée (tests, standards, analysis)
- Déploiements petits = moins de risque

---

## Tâche: Déployer Symfony sur GCP

### Parallèle GCP / AWS

| Étape | AWS | GCP |
|-------|-----|-----|
| **Image Docker** | ECR | Container Registry |
| **Container Orchestration** | ECS / EKS | Cloud Run / GKE |
| **Base de données** | RDS | Cloud SQL |
| **Secrets** | Secrets Manager | Secret Manager |
| **CI/CD** | CodePipeline | Cloud Build |
| **Monitoring** | CloudWatch | Cloud Monitoring |

### Déploiement Simplifié

#### 1. Préparation
- Dockerfile
- Configuration .env.production
- Cloud SQL provisionnée

#### 2. Push Image
```bash
docker build -t gcr.io/PROJECT/app .
docker push gcr.io/PROJECT/app
```
*(Équivalent: ECR push avec `aws ecr`)*

#### 3. Déployer Cloud Run
```bash
gcloud run deploy app \
  --image gcr.io/PROJECT/app \
  --region europe-west1
```
*(Équivalent: `aws ecs update-service` ou ECS Fargate)*

#### 4. Configuration
- Environment variables + secrets
- Cloud SQL connexion
- Health endpoint `/health`
- Domain personnalisé

#### 5. Monitoring & Logs
- Cloud Logging (CloudWatch équivalent)
- Cloud Monitoring (CloudWatch metrics)

#### 6. CI/CD (Optional)
Cloud Build déclenche auto à chaque push
*(Équivalent: CodePipeline)*

---

## Navigation

**[← Retour au README](../../README.md)**