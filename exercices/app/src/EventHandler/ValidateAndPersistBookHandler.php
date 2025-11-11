<?php

namespace App\EventHandler;

use App\Entity\Book;
use App\Event\BookImportedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsEventListener(event: BookImportedEvent::class)]
final class ValidateAndPersistBookHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function __invoke(BookImportedEvent $event): void
    {
        $data = $event->getData();

        $book = new Book();
        $book->setTitle($data['title']);
        $book->setAuthor($data['author']);
        $book->setYear($data['year']);
        $book->setIsbn($data['isbn']);

        $errors = $this->validator->validate($book);
        if (count($errors) > 0) {
            throw new \InvalidArgumentException(
                sprintf('Validation failed for book "%s": %s', $data['title'], (string) $errors)
            );
        }

        $this->entityManager->persist($book);
        $this->entityManager->flush();
    }
}