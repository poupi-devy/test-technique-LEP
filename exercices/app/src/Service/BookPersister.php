<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\BookImportData;
use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;

final class BookPersister
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function persist(BookImportData $bookData): Book
    {
        $book = new Book();
        $book->setTitle($bookData->title);
        $book->setAuthor($bookData->author);
        $book->setYear($bookData->year);
        $book->setIsbn($bookData->isbn);

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $book;
    }
}