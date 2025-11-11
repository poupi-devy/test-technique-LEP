<?php

declare(strict_types=1);

namespace App\EventHandler;

use App\Entity\Book;
use App\Event\BookImportedEvent;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
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
        $bookData = $event->getBookData();

        $book = new Book();
        $book->setTitle($bookData->title);
        $book->setAuthor($bookData->author);
        $book->setYear($bookData->year);
        $book->setIsbn($bookData->isbn);

        $errors = $this->validator->validate($book);
        if (count($errors) > 0) {
            throw new InvalidArgumentException(
                sprintf('Validation failed for book "%s": %s', $bookData->title, (string) $errors)
            );
        }

        $this->entityManager->persist($book);
        $this->entityManager->flush();
    }
}
