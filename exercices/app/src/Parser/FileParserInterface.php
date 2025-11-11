<?php

declare(strict_types=1);

namespace App\Parser;

/**
 * Abstraction for parsing various file formats
 * Enables extensibility for CSV, Excel, JSON, etc.
 */
interface FileParserInterface
{
    /**
     * Parse a file and yield rows
     *
     * @return \Iterator<int, list<string|null>>
     */
    public function parse(string $filePath): \Iterator;
}
