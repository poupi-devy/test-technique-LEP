<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\DTO\BookImportData;
use App\Entity\Book;
use App\Service\BookPersister;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class BookPersisterTest extends TestCase
{
    private BookPersister $persister;

    private \PHPUnit\Framework\MockObject\MockObject $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->persister = new BookPersister($this->entityManager);
    }

    public function testPersistCallsEntityManagerPersistAndFlush(): void
    {
        $bookData = new BookImportData(
            title: 'Test Book',
            author: 'Test Author',
            year: 2024,
            isbn: '9780201633610',
        );

        $this->entityManager
            ->expects(self::once())
            ->method('persist');

        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        $this->persister->persist($bookData);
    }

    public function testPersistCreatesBookEntityWithCorrectData(): void
    {
        $bookData = new BookImportData(
            title: 'Test Book',
            author: 'Test Author',
            year: 2024,
            isbn: '9780201633610',
        );

        $persistedBooks = [];
        $this->entityManager
            ->method('persist')
            ->willReturnCallback(function ($entity) use (&$persistedBooks): void {
                $persistedBooks[] = $entity;
            });

        $this->persister->persist($bookData);

        self::assertCount(1, $persistedBooks);
        $book = $persistedBooks[0];
        self::assertInstanceOf(Book::class, $book);
        self::assertSame('Test Book', $book->getTitle());
        self::assertSame('Test Author', $book->getAuthor());
        self::assertSame(2024, $book->getYear());
        self::assertSame('9780201633610', $book->getIsbn());
    }

    public function testPersistWithMinimalData(): void
    {
        $bookData = new BookImportData(
            title: 'A',
            author: 'B',
            year: 1000,
            isbn: '9780201633610',
        );

        $this->entityManager->expects(self::once())->method('persist');
        $this->entityManager->expects(self::once())->method('flush');

        $this->persister->persist($bookData);
    }

    public function testPersistWithLongTitle(): void
    {
        $longTitle = str_repeat('a', 255);
        $bookData = new BookImportData(
            title: $longTitle,
            author: 'Author',
            year: 2024,
            isbn: '9780201633610',
        );

        $persistedBooks = [];
        $this->entityManager
            ->method('persist')
            ->willReturnCallback(function ($entity) use (&$persistedBooks): void {
                $persistedBooks[] = $entity;
            });

        $this->persister->persist($bookData);

        self::assertSame($longTitle, $persistedBooks[0]->getTitle());
    }

    public function testPersistFlushIsCalledAfterPersist(): void
    {
        $bookData = new BookImportData(
            title: 'Book',
            author: 'Author',
            year: 2024,
            isbn: '9780201633610',
        );

        $callOrder = [];

        $this->entityManager
            ->method('persist')
            ->willReturnCallback(function () use (&$callOrder): void {
                $callOrder[] = 'persist';
            });

        $this->entityManager
            ->method('flush')
            ->willReturnCallback(function () use (&$callOrder): void {
                $callOrder[] = 'flush';
            });

        $this->persister->persist($bookData);

        self::assertSame(['persist', 'flush'], $callOrder);
    }

    public function testPersistMultipleTimes(): void
    {
        $bookData1 = new BookImportData(
            title: 'Book 1',
            author: 'Author 1',
            year: 2024,
            isbn: '9780201633610',
        );

        $bookData2 = new BookImportData(
            title: 'Book 2',
            author: 'Author 2',
            year: 2023,
            isbn: '9780134685991',
        );

        $this->entityManager
            ->expects(self::exactly(2))
            ->method('persist');

        $this->entityManager
            ->expects(self::exactly(2))
            ->method('flush');

        $this->persister->persist($bookData1);
        $this->persister->persist($bookData2);
    }
}
