<?php

declare(strict_types=1);

namespace App\EventHandler;

use App\Entity\Book;
use App\Event\BookImportedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsEventListener(event: BookImportedEvent::class)]
final readonly class ValidateAndPersistBookHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface     $validator,
    ) {
    }

    public function __invoke(BookImportedEvent $event): void
    {
        $data = $event->getData();

        $book = new Book();
        if (isset($data['title']) && is_string($data['title'])) {
            $book->setTitle($data['title']);
        }

        if (isset($data['author']) && is_string($data['author'])) {
            $book->setAuthor($data['author']);
        }

        if (isset($data['year']) && is_int($data['year'])) {
            $book->setYear($data['year']);
        }

        if (isset($data['isbn']) && is_string($data['isbn'])) {
            $book->setIsbn($data['isbn']);
        }

        $errors = $this->validator->validate($book);
        if (count($errors) > 0) {
            $title = isset($data['title']) && is_string($data['title']) ? $data['title'] : 'Unknown';
            throw new \InvalidArgumentException(
                sprintf('Validation failed for book "%s": %s', $title, (string) $errors)
            );
        }

        $this->entityManager->persist($book);
        $this->entityManager->flush();
    }
}
