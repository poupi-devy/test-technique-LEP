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
            $headerLine = fgetcsv($file);

            if ($headerLine === false) {
                throw new \RuntimeException('CSV file is empty or cannot be read');
            }

            while (($row = fgetcsv($file)) !== false) {
                /** @var list<string|null> $row */
                // Normalize array to ensure all values are string|null
                yield array_map(
                    static fn ($value): ?string => $value === '' ? null : (string) $value,
                    $row
                );
            }
        } finally {
            fclose($file);
        }
    }
}
