<?php

declare(strict_types=1);

namespace App\Parser;

final class CsvFileParser implements FileParserInterface
{
    /**
     * Parse a CSV file and yield each row
     *
     * @return \Iterator<int, list<string|null>>
     */
    public function parse(string $filePath): \Iterator
    {
        $file = fopen($filePath, 'r');

        if (!$file) {
            throw new \RuntimeException(sprintf('Unable to open file: %s', $filePath));
        }

        try {
            // Skip header line
            fgetcsv($file);

            while (($row = fgetcsv($file)) !== false) {
                /** @var list<string|null> $row */
                yield $row;
            }
        } finally {
            fclose($file);
        }
    }
}
