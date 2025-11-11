# I. Fondamentaux PHP et Symfony - Exercice Pratique : Commande CSV

**[← Retour au README](../../README.md)** | **[← Composants Symfony](./composants-symfony.md)**

---

## Scénario

Vous devez créer une **commande Symfony** pour importer des données de livres à partir d'un fichier CSV. Le fichier contient :
- **titre** : Titre du livre
- **auteur** : Nom de l'auteur
- **année** : Année de publication
- **isbn** : Numéro ISBN

### Résultats attendus
- ✅ Lecture du fichier CSV
- ✅ Validation des données
- ✅ Création des enregistrements en base de données
- ✅ Gestion des erreurs et logs
- ✅ Rapport d'import (succès, erreurs)

---

## Architecture et Approche

### Principes appliqués
- **SOLID**: Single Responsibility pour chaque classe
- **Type-safe**: PHP 8.4 avec typage fort
- **Event-Driven**: Événements de domaine pour notifications
- **DTO Pattern**: Validation et transfert de données séparés
- **Repository Pattern**: Abstraction de la persistance
- **Immutabilité**: Propriétés readonly où applicable

### Flux d'exécution

```
┌─────────────────────────────────┐
│  BookImportCommand (CLI Entry)  │
└────────────┬────────────────────┘
             │
             ▼
┌─────────────────────────────────┐
│  CSV Reader (Iterator)          │
│  Parcourt le fichier ligne/ligne│
└────────────┬────────────────────┘
             │
             ▼
┌─────────────────────────────────┐
│  DTO + Validator                │
│  BookImportRequest (validation) │
└────────────┬────────────────────┘
             │
             ▼
┌─────────────────────────────────┐
│  BookImportHandler (invocable)  │
│  Crée et persiste l'entité      │
└────────────┬────────────────────┘
             │
             ▼
┌─────────────────────────────────┐
│  Events Dispatched              │
│  BookImportedEvent              │
│  → Logging, notifications, etc. │
└─────────────────────────────────┘
```

---

## Structure des Classes

### 1. DTO : BookImportRequest

```php
final class BookImportRequest {
    #[Assert\NotBlank(message: 'Le titre est obligatoire')]
    #[Assert\Length(['min' => 3, 'max' => 255])]
    public string $title;

    #[Assert\NotBlank(message: 'L\'auteur est obligatoire')]
    public string $author;

    #[Assert\NotBlank(message: 'L\'année est obligatoire')]
    #[Assert\Positive(message: 'L\'année doit être positive')]
    #[Assert\Range(['min' => 1000, 'max' => date('Y') + 1])]
    public int $year;

    #[Assert\NotBlank(message: 'L\'ISBN est obligatoire')]
    #[Assert\Isbn(['version' => '10,13'])]
    public string $isbn;
}
```

### 2. Repository : BookRepository

Interface métier :
```php
interface BookRepositoryInterface {
    public function save(Book $book): void;
    public function findByIsbn(string $isbn): ?Book;
}
```

Implémentation Doctrine :
```php
final class DoctrineBookRepository implements BookRepositoryInterface {
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) { }

    public function save(Book $book): void {
        $this->entityManager->persist($book);
        $this->entityManager->flush();
    }

    public function findByIsbn(string $isbn): ?Book {
        return $this->entityManager->getRepository(Book::class)
            ->findOneBy(['isbn' => $isbn]);
    }
}
```

### 3. Handler : BookImportHandler

```php
final class BookImportHandler {
    public function __construct(
        private readonly BookRepositoryInterface $repository,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly ValidatorInterface $validator,
    ) { }

    public function __invoke(BookImportRequest $request): Book {
        // Validation
        $violations = $this->validator->validate($request);
        if (count($violations) > 0) {
            throw new ValidationException('Données invalides', $violations);
        }

        // Vérifier doublon
        $existing = $this->repository->findByIsbn($request->isbn);
        if ($existing) {
            throw new DuplicateBookException("ISBN {$request->isbn} déjà importé");
        }

        // Créer l'entité
        $book = new Book(
            title: $request->title,
            author: $request->author,
            year: $request->year,
            isbn: $request->isbn,
        );

        // Persister
        $this->repository->save($book);

        // Dispatcher événement
        $this->dispatcher->dispatch(new BookImportedEvent($book));

        return $book;
    }
}
```

### 4. Entité : Book

```php
#[ORM\Entity]
#[ORM\Table(name: 'books')]
final class Book {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'string', length: 255)]
    private string $author;

    #[ORM\Column(type: 'integer')]
    private int $year;

    #[ORM\Column(type: 'string', length: 20, unique: true)]
    private string $isbn;

    public function __construct(
        string $title,
        string $author,
        int $year,
        string $isbn,
    ) {
        $this->title = $title;
        $this->author = $author;
        $this->year = $year;
        $this->isbn = $isbn;
    }

    // Getters (readonly access)
    public function getId(): int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getAuthor(): string { return $this->author; }
    public function getYear(): int { return $this->year; }
    public function getIsbn(): string { return $this->isbn; }
}
```

### 5. Événement : BookImportedEvent

```php
final class BookImportedEvent {
    public function __construct(
        private readonly Book $book,
    ) { }

    public function getBook(): Book {
        return $this->book;
    }
}
```

### 6. Listener : LogBookImportListener

```php
final class LogBookImportListener {
    public function __construct(
        private readonly LoggerInterface $logger,
    ) { }

    #[AsEventListener(event: BookImportedEvent::class)]
    public function onBookImported(BookImportedEvent $event): void {
        $book = $event->getBook();
        $this->logger->info("Livre importé: {$book->getTitle()} ({$book->getIsbn()})");
    }
}
```

### 7. Commande : BookImportCommand

```php
#[AsCommand(
    name: 'app:import:books',
    description: 'Importer des livres à partir d\'un fichier CSV'
)]
final class BookImportCommand extends Command {
    public function __construct(
        private readonly BookImportHandler $handler,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function configure(): void {
        $this->addArgument(
            'file',
            InputArgument::REQUIRED,
            'Chemin du fichier CSV'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $filePath = $input->getArgument('file');

        if (!file_exists($filePath)) {
            $output->writeln("<error>Fichier non trouvé: {$filePath}</error>");
            return Command::FAILURE;
        }

        $io = new SymfonyStyle($input, $output);
        $io->title('Importation des livres');

        $successCount = 0;
        $errorCount = 0;

        try {
            foreach ($this->readCsv($filePath) as $rowNumber => $data) {
                try {
                    $request = new BookImportRequest(
                        title: $data['title'] ?? '',
                        author: $data['author'] ?? '',
                        year: (int)($data['year'] ?? 0),
                        isbn: $data['isbn'] ?? '',
                    );

                    ($this->handler)($request);
                    $successCount++;
                    $io->success("✓ Ligne {$rowNumber}: {$request->title}");
                } catch (ValidationException $e) {
                    $errorCount++;
                    $io->error("✗ Ligne {$rowNumber}: {$e->getMessage()}");
                    $this->logger->error("Import error line {$rowNumber}", [
                        'errors' => $e->getViolations(),
                    ]);
                }
            }
        } catch (Exception $e) {
            $io->error("Erreur lors de l'importation: {$e->getMessage()}");
            return Command::FAILURE;
        }

        $io->newLine();
        $io->section('Résumé');
        $io->text("Importés: <info>{$successCount}</info>");
        $io->text("Erreurs: <error>{$errorCount}</error>");

        return $errorCount > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    private function readCsv(string $filePath): iterable {
        if (($handle = fopen($filePath, 'r')) === false) {
            throw new RuntimeException("Impossible d'ouvrir {$filePath}");
        }

        try {
            $headers = fgetcsv($handle);
            if (!$headers) {
                throw new RuntimeException('Fichier CSV vide ou mal formé');
            }

            $rowNumber = 1;
            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;
                $data = array_combine($headers, $row);
                if ($data === false) {
                    continue;
                }
                yield $rowNumber => $data;
            }
        } finally {
            fclose($handle);
        }
    }
}
```

---

## Utilisation

### Format du fichier CSV

```csv
title,author,year,isbn
"La Récréation",Jean Christophe Vignal,2014,9782253177579
"L'Alchimiste",Paulo Coelho,1988,9782253129882
"1984",George Orwell,1949,9782264035461
```

### Exécution de la commande

```bash
php bin/console app:import:books data/books.csv
```

### Résultat

```
Importation des livres
======================

✓ Ligne 2: La Récréation
✓ Ligne 3: L'Alchimiste
✓ Ligne 4: 1984

Résumé
======

Importés: 3
Erreurs: 0
```

---

## Tests

Les tests unitaires et fonctionnels couvrent :
- **Validation**: DTO avec données invalides
- **Doublons**: ISBN déjà existant
- **Fichier manquant**: Gestion d'erreur
- **CSV malformé**: Format incorrect
- **Événements**: Vérification que BookImportedEvent est dispatché
- **Logging**: Enregistrement des succès/erreurs
- **Persévérance**: Continuer même en cas d'erreur sur une ligne

---

## Code Source

Implémentation complète disponible dans : **[exercises/01-commande-csv/](../../exercises/01-commande-csv/)**

---

## Navigation

**[← Retour au README](../../README.md)** | **[← Composants Symfony](./composants-symfony.md)** | **[Section II. Bases de données →](../II-bases-donnees/questions-conceptuelles.md)**