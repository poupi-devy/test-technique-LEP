<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\BookImportData;

final class BookImportHydrator
{
    private const COLUMN_TITLE = 0;

    private const COLUMN_AUTHOR = 1;

    private const COLUMN_YEAR = 2;

    private const COLUMN_ISBN = 3;

    /**
     * @param list<string|null> $row
     */
    public function hydrate(array $row): BookImportData
    {
        return new BookImportData(
            title: $row[self::COLUMN_TITLE] ?? '',
            author: $row[self::COLUMN_AUTHOR] ?? '',
            year: (int) ($row[self::COLUMN_YEAR] ?? 0),
            isbn: $row[self::COLUMN_ISBN] ?? '',
        );
    }
}
