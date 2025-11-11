# I. Fondamentaux PHP et Symfony - Questions Conceptuelles

**[← Retour au README](../../README.md)**

---

## Q1. Interface vs Classe Abstraite

### Définition

**Interface**:
- Contrat pour les classes qui l'implémentent
- Définit uniquement les signatures de méthodes (PHP 8.4+ accepte les constantes et méthodes privées)
- Aucune implémentation
- Une classe peut implémenter plusieurs interfaces

**Classe Abstraite**:
- Classe parente pour les classes enfants
- Peut contenir des méthodes abstraites ET implémentées
- Peut avoir des propriétés avec des visibilités différentes
- Une classe ne peut hériter que d'une seule classe abstraite

### Quand Utiliser Quoi?

- **Interface**: Quand vous voulez définir un contrat sans présupposer l'implémentation
- **Classe Abstraite**: Quand vous voulez partager du code commun entre plusieurs classes

---

## Q2. Principes SOLID

### Single Responsibility Principle (SRP)
Une classe doit avoir une **seule raison de changer**. Chaque classe doit avoir une responsabilité unique.


### Open/Closed Principle (OCP)
Les classes doivent être **ouvertes à l'extension, fermées à la modification**.


### Liskov Substitution Principle (LSP)
Les objets d'une classe dérivée doivent pouvoir **remplacer** les objets de la classe parent sans casser l'application.


### Interface Segregation Principle (ISP)
Les clients ne doivent **pas dépendre d'interfaces qu'ils n'utilisent pas**.


### Dependency Inversion Principle (DIP)
Dépendre des **abstractions, pas des implémentations concrètes**.


### Impact sur la Maintenabilité et Évolutivité

- **Flexibilité**: Facile de changer les implémentations
- **Testabilité**: Facile de mocker les dépendances
- **Réutilisabilité**: Code modulaire et composable
- **Évitabilité**: Moins de couplage entre les classes
- **Maintenance**: Modifications localisées sans effet de bord

---

## Q3. Design Patterns

### Définition
Les design patterns sont des **solutions réutilisables** à des problèmes communs en programmation. Ils offrent des templates pour écrire du code maintenable et évolutif.

### Patterns Utilisés

#### 1. Singleton Pattern
**Problème**: Assurer qu'une classe n'a qu'une seule instance.

**Quand l'utiliser**: Logger, configurations, connexion DB (bien que l'injection soit préférée)

#### 2. Factory Pattern
**Problème**: Créer des objets sans spécifier leurs classes concrètes.

**Quand l'utiliser**: Création d'objets basée sur des conditions

#### 3. Strategy Pattern
**Problème**: Encapsuler plusieurs algorithmes interchangeables.

**Quand l'utiliser**: Algorithmes alternatifs (prix, tri, filtrage)

#### 4. Observer Pattern
**Problème**: Notifier plusieurs objets d'un changement d'état.

**Utilisé dans Symfony via Event Dispatcher** (voir Q5)

#### 5. Dependency Injection Pattern
**Problème**: Réduire le couplage entre les classes.

**Quand l'utiliser**: Presque toujours pour un code découplé

---

## Q4. Injection de Dépendances (DI)

### Concept
L'injection de dépendances consiste à **fournir les dépendances d'une classe via son constructeur** ou des setters plutôt que de les créer à l'intérieur de la classe.

### Avantages

**1. Testabilité**

**2. Flexibilité**
```php
// ✅ Facile de changer l'implémentation
$service = new UserService(new DoctrineRepository());
$service = new UserService(new RedisRepository());
```

**3. Découplage**
- Les classes ne connaissent pas comment créer leurs dépendances
- Facilite la modification sans impact en cascade

**4. Maintenabilité**
- Les dépendances sont explicites
- Facile de comprendre les interactions

### Comment Symfony Facilite la DI

#### 1. Service Container
Symfony gère automatiquement l'instantiation et l'injection des services.

```yaml
# config/services.yaml
services:
    App\Repository\UserRepository:
        arguments:
            - '@doctrine.orm.entity_manager'

    App\Service\UserService:
        arguments:
            - '@App\Repository\UserRepository'
            - '@App\Service\EmailService'
```

#### 1bis. Autowiring avec Attributes (PHP 8.0+)
Approche plus moderne sans configuration YAML - utiliser les attributes directement dans la classe.

```php
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class UserService {
    public function __construct(
        #[Autowire(service: 'App\Repository\UserRepository')]
        private UserRepository $repository,

        #[Autowire(service: 'App\Service\EmailService')]
        private EmailService $email,

        // Ou simplement laisser l'autowiring automatique pour les types
        private Logger $logger  // Autowire automatiquement par type
    ) { }
}
```

**Avantages**:
- Configuration en PHP, pas en YAML
- Plus proche du code
- Type-safe et refactorisable
- Parfait pour PHP 8.4 avec typage fort

#### 2. Autowiring Automatique
```php
class UserService {
    public function __construct(
        private UserRepository $repository,    // Autowire automatique
        private EmailService $email            // Autowire automatique
    ) { }
}
```

#### 3. Injection dans les Contrôleurs
```php
final class UserController {
    public function create(
        UserService $userService,     // Injected automatiquement par Symfony
        EntityManagerInterface $em
    ): Response {
        // $userService et $em sont déjà instanciés
    }
}
```

#### 4. Binding des Interfaces
```php
# config/services.yaml
services:
    PaymentProcessorInterface: '@Stripe\Payment'

    # Maintenant, chaque injection de PaymentProcessorInterface recevra Stripe\Payment
```

---

## Q5. Event Dispatcher de Symfony

### Concept
L'Event Dispatcher est un système de **publication/abonnement** qui permet aux composants de communiquer sans se connaître directement.

### Architecture

```
┌─────────────────────┐
│   Event Source      │
│   (dispatche event) │
└──────┬──────────────┘
       │
┌──────▼────────┐
│   Dispatcher  │
└──────┬────────┘
       │
┌──────▼──────────────────┐
│   Listeners/Subscribers │
│   (écoutent l'event)    │
└─────────────────────────┘
```

### Avantages

- **Découplage**: UserService ne connaît pas les listeners
- **Extensibilité**: Ajouter des listeners sans modifier UserService
- **Maintenabilité**: Chaque listener a une responsabilité unique
- **Testabilité**: Facile de tester isolément

---

## Q6. CSRF Tokens dans les Formulaires Symfony

### Concept
Le **CSRF (Cross-Site Request Forgery)** est une attaque où un attaquant force un utilisateur à effectuer une action non désirée. Les tokens CSRF préviennent cette attaque.

### Fonctionnement

```
1. Utilisateur charge le formulaire
   ├─ Serveur génère un token CSRF aléatoire
   ├─ Token associé à la session de l'utilisateur
   └─ Token inséré dans le formulaire (hidden field)

2. Utilisateur soumet le formulaire
   ├─ Token est envoyé avec les données
   └─ Serveur vérifie que le token correspond à la session

3. Attaquant essaie d'envoyer une requête
   ├─ Pas accès au token de la session
   └─ Requête est rejetée
```

### Sécurité

**Pourquoi c'est efficace**:
- Le token est **aléatoire et unique** par session
- L'attaquant **ne peut pas accéder** au token de l'utilisateur
- Le token est **validé côté serveur** avant traitement

---
## Navigation

**[← Retour au README](../../README.md)** | **[Composants Symfony →](./composants-symfony.md)**
