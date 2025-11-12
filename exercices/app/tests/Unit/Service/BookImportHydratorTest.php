<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\DTO\BookImportData;
use App\Service\BookImportHydrator;
use PHPUnit\Framework\TestCase;

final class BookImportHydratorTest extends TestCase
{
    private BookImportHydrator $hydrator;

    protected function setUp(): void
    {
        $this->hydrator = new BookImportHydrator();
    }

    public function testHydrateWithValidData(): void
    {
        $row = ['Test Book', 'Test Author', '2024', '9780201633610'];

        $result = $this->hydrator->hydrate($row);

        self::assertInstanceOf(BookImportData::class, $result);
        self::assertSame('Test Book', $result->title);
        self::assertSame('Test Author', $result->author);
        self::assertSame(2024, $result->year);
        self::assertSame('9780201633610', $result->isbn);
    }

    public function testHydrateWithNullValues(): void
    {
        $row = [null, null, null, null];

        $result = $this->hydrator->hydrate($row);

        self::assertSame('', $result->title);
        self::assertSame('', $result->author);
        self::assertSame(0, $result->year);
        self::assertSame('', $result->isbn);
    }

    public function testHydrateWithMissingColumns(): void
    {
        $row = ['Test Book'];

        $result = $this->hydrator->hydrate($row);

        self::assertSame('Test Book', $result->title);
        self::assertSame('', $result->author);
        self::assertSame(0, $result->year);
        self::assertSame('', $result->isbn);
    }

    public function testHydrateWithNonNumericYear(): void
    {
        $row = ['Book', 'Author', 'invalid_year', 'ISBN'];

        $result = $this->hydrator->hydrate($row);

        self::assertSame(0, $result->year);
    }

    public function testHydrateWithEmptyStrings(): void
    {
        $row = ['', '', '', ''];

        $result = $this->hydrator->hydrate($row);

        self::assertSame('', $result->title);
        self::assertSame('', $result->author);
        self::assertSame(0, $result->year);
        self::assertSame('', $result->isbn);
    }
}
