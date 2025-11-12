<?php

declare(strict_types=1);

namespace App\Tests\Unit\Parser;

use App\Parser\CsvFileParser;
use PHPUnit\Framework\TestCase;

final class CsvFileParserTest extends TestCase
{
    private CsvFileParser $parser;

    private string $testDir;

    protected function setUp(): void
    {
        $this->parser = new CsvFileParser();
        $this->testDir = sys_get_temp_dir() . '/csv_test_' . uniqid();
        @mkdir($this->testDir, 0777, true);
    }

    protected function tearDown(): void
    {
        // Clean up test files
        if (is_dir($this->testDir)) {
            $files = glob($this->testDir . '/*');
            if (is_array($files)) {
                foreach ($files as $file) {
                    @unlink($file);
                }
            }
            @rmdir($this->testDir);
        }
    }

    public function testParseValidCsv(): void
    {
        $csvPath = $this->createCsvFile([
            ['title', 'author', 'year', 'isbn'],
            ['Book 1', 'Author 1', '2024', '9780201633610'],
            ['Book 2', 'Author 2', '2023', '9780134685991'],
        ]);

        $rows = iterator_to_array($this->parser->parse($csvPath));

        self::assertCount(2, $rows);
        self::assertSame(['Book 1', 'Author 1', '2024', '9780201633610'], $rows[0]);
        self::assertSame(['Book 2', 'Author 2', '2023', '9780134685991'], $rows[1]);
    }

    public function testParseWithEmptyValues(): void
    {
        $csvPath = $this->createCsvFile([
            ['title', 'author', 'year', 'isbn'],
            ['Book', '', '2024', ''],
        ]);

        $rows = iterator_to_array($this->parser->parse($csvPath));

        self::assertCount(1, $rows);
        self::assertSame(['Book', null, '2024', null], $rows[0]);
    }

    public function testParseWithMoreColumnsThanHeader(): void
    {
        $csvPath = $this->createCsvFile([
            ['title', 'author'],
            ['Book', 'Author', 'Extra', 'More'],
        ]);

        $rows = iterator_to_array($this->parser->parse($csvPath));

        self::assertCount(1, $rows);
        self::assertCount(4, $rows[0]);
    }


    /**
     * @param list<list<string>> $rows
     */
    private function createCsvFile(array $rows): string
    {
        $filePath = $this->testDir . '/test_' . uniqid() . '.csv';
        $fp = fopen($filePath, 'w');

        if (!$fp) {
            throw new \RuntimeException('Cannot create test CSV file');
        }

        foreach ($rows as $row) {
            fputcsv($fp, $row);
        }

        fclose($fp);

        return $filePath;
    }
}
