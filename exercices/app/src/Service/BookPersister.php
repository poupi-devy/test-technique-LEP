<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\BookImportData;
use App\Entity\Book as BookEntity;
use Doctrine\ORM\EntityManagerInterface;

final readonly class BookPersister
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function persist(BookImportData $bookData): void
    {
        $book = new BookEntity();
        $book->setTitle($bookData->title);
        $book->setAuthor($bookData->author);
        $book->setYear($bookData->year);
        $book->setIsbn($bookData->isbn);

        $this->entityManager->persist($book);
        $this->entityManager->flush();
    }
}
