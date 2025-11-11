# I. Fondamentaux PHP et Symfony - Composants Symfony

**[← Retour au README](../../README.md)** | **[← Questions Conceptuelles](./questions-conceptuelles.md)**

---

## Routage

### Concept
Le composant Routage (Router) mappe les requêtes HTTP entrantes à des actions de contrôleur. Il transforme une URL en paramètres de route.

### Définition des Routes

#### Avec Attributes (PHP 8.0+ - Recommandé)
```php
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class ProductController extends AbstractController {
    #[Route('/products', name: 'product_list', methods: ['GET'])]
    public function list(): Response {
        return $this->render('product/list.html.twig');
    }

    #[Route('/products/{id}', name: 'product_show', methods: ['GET'])]
    public function show(int $id): Response {
        return $this->render('product/show.html.twig', ['id' => $id]);
    }

    #[Route('/products', name: 'product_create', methods: ['POST'])]
    public function create(): Response {
        // Créer un produit
        return $this->redirectToRoute('product_list');
    }

    #[Route('/products/{id}', name: 'product_update', methods: ['PUT', 'PATCH'])]
    public function update(int $id): Response {
        // Mettre à jour un produit
        return $this->redirectToRoute('product_show', ['id' => $id]);
    }

    #[Route('/products/{id}', name: 'product_delete', methods: ['DELETE'])]
    public function delete(int $id): Response {
        // Supprimer un produit
        return $this->redirectToRoute('product_list');
    }
}
```

#### Avec Fichier YAML (Legacy)
```yaml
# config/routes.yaml
product_list:
    path: /products
    controller: App\Controller\ProductController::list
    methods: [GET]

product_show:
    path: /products/{id}
    controller: App\Controller\ProductController::show
    methods: [GET]
    requirements:
        id: '\d+'  # L'ID doit être un nombre
```

### Options de Route

Les options permettent de contrôler le comportement : `path` (URL), `methods` (GET/POST), `requirements` (contraintes regex), `name` (identifiant), `defaults` (valeurs par défaut), `host` (sous-domaine), `schemes` (http/https).

### Convertisseurs de Paramètres

Les convertisseurs transforment automatiquement les paramètres d'URL en objets métier. Par exemple, `#[Route('/products/{product}')]` récupère directement l'objet Product depuis l'ID, avec gestion automatique des 404.

---

## Services

### Concept
Un service est une classe réutilisable qui effectue une tâche spécifique. Les services sont gérés par le Service Container de Symfony.

### Définition d'un Service

#### Avec Attributes (PHP 8.0+)
```php
use Symfony\Component\DependencyInjection\Attribute\AsService;
use Psr\Log\LoggerInterface;

#[AsService]
final class ProductService {
    public function __construct(
        private readonly ProductRepository $repository,
        private readonly LoggerInterface $logger,
        private readonly EventDispatcherInterface $dispatcher
    ) { }

    public function createProduct(string $name, float $price): Product {
        $product = new Product($name, $price);
        $this->repository->save($product);

        $this->logger->info("Product created: {$name}");
        $this->dispatcher->dispatch(new ProductCreatedEvent($product));

        return $product;
    }

    public function updateProduct(Product $product, string $name, float $price): void {
        $product->setName($name);
        $product->setPrice($price);
        $this->repository->save($product);

        $this->logger->info("Product updated: {$name}");
    }

    public function deleteProduct(Product $product): void {
        $this->repository->delete($product);
        $this->logger->info("Product deleted: {$product->getName()}");
    }
}
```

---

## Formulaires

### Concept
Le composant Formulaires simplifie la création, la validation et le traitement des formulaires HTML.

### Créer un FormType

```php
final class ProductType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('name', TextType::class)
            ->add('price', MoneyType::class)
            ->add('description', TextareaType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults(['data_class' => CreateProductRequest::class]);
    }
}
```

### Validation

**Avec Attributes** (recommandé):
```php
#[Assert\NotBlank]
#[Assert\Length(['min' => 3, 'max' => 255])]
private string $name;

#[Assert\Positive]
private float $price;
```

**Ou via validation.yaml** pour la configuration centralisée.

### Affichage Twig

```twig
{{ form_start(form) }}
    {{ form_widget(form) }}
{{ form_end(form) }}
```

### Approche Moderne : DTO + Event + Handler Invocable

La façon moderne et scalable utilise un **DTO** (valide les données), un **Handler invocable** (action unique), et un **événement de domaine** (découplage).

**1. DTO** (valide les données):
```php
final class CreateProductRequest {
    #[Assert\NotBlank]
    public string $name;

    #[Assert\Positive]
    public float $price;
}
```

**2. Handler invocable** (responsabilité unique):
```php
final class CreateProductHandler {
    public function __invoke(CreateProductRequest $request, ProductService $service, EventDispatcher $dispatcher): Response {
        $product = $service->createProduct($request->name, $request->price);
        $dispatcher->dispatch(new ProductCreatedEvent($product));
        return $this->redirectToRoute('product_show', ['id' => $product->getId()]);
    }
}
```

**3. Dans le contrôleur**:
```php
#[Route('/products', methods: ['POST'])]
public function create(Request $request, CreateProductHandler $handler): Response {
    $dto = new CreateProductRequest();
    $form = $this->createForm(ProductType::class, $dto);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        return $handler($form->getData());
    }

    return $this->render('product/create.html.twig', ['form' => $form]);
}
```

### Pourquoi cette approche?

- **DTO**: Sépare les données du formulaire de l'entité métier. Validation centralisée, facile à tester.
- **Event**: Les autres systèmes (email, logs, cache, analytics) s'enregistrent comme listeners. Zéro couplage.
- **Handler Invocable**: Une classe = une action métier. Facile à tester, réutiliser, maintenir. Responsabilité unique.
- **CSRF automatique**: Géré par Symfony dans `form_end(form)` sans effort.

C'est l'approche **Event-Driven + Separation of Concerns** avec architecture scalable.

---

## Navigation

**[← Retour au README](../../README.md)** | **[Questions Conceptuelles](./questions-conceptuelles.md)** | **[Exercice Commande CSV →](./exercice-commande-csv.md)**