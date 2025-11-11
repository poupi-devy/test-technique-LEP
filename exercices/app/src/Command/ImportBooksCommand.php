<?php

declare(strict_types=1);

namespace App\Command;

use App\DTO\BookImportData;
use App\Event\BookImportedEvent;
use Exception;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[AsCommand(
    name: 'app:import:books',
    description: 'Import books from a CSV file into the database',
)]
final readonly class ImportBooksCommand
{
    private const EXPECTED_COLUMNS = 4;
    private const COLUMN_TITLE = 0;
    private const COLUMN_AUTHOR = 1;
    private const COLUMN_YEAR = 2;
    private const COLUMN_ISBN = 3;

    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function __invoke(#[Argument(description: 'Path to the CSV file containing book data', name: 'file')]
        string $file, SymfonyStyle $io): int
    {
        $filePath = $file;

        if (!file_exists($filePath)) {
            $io->error(sprintf('File not found: %s', $filePath));
            return Command::FAILURE;
        }

        $io->info(sprintf('Starting import from file: %s', $filePath));

        $file = fopen($filePath, 'r');
        if (!$file) {
            $io->error('Unable to open file');
            return Command::FAILURE;
        }

        $importedCount = 0;
        $errorCount = 0;

        try {
            // Skip header line
            fgetcsv($file);

            while (($row = fgetcsv($file)) !== false) {
                /** @var list<string|null> $row */
                if (count($row) < self::EXPECTED_COLUMNS) {
                    $errorCount++;
                    $io->warning(sprintf('Invalid row (expecting %d columns): %s', self::EXPECTED_COLUMNS, implode(',', $row)));
                    continue;
                }

                try {
                    $bookData = new BookImportData(
                        title: $row[self::COLUMN_TITLE] ?? '',
                        author: $row[self::COLUMN_AUTHOR] ?? '',
                        year: (int) ($row[self::COLUMN_YEAR] ?? 0),
                        isbn: $row[self::COLUMN_ISBN] ?? '',
                    );

                    $event = new BookImportedEvent($bookData);
                    $this->eventDispatcher->dispatch($event);
                    $importedCount++;

                    $io->writeln(sprintf('✓ Imported: %s', $bookData->title));
                } catch (Exception $e) {
                    $errorCount++;
                    $io->warning(sprintf('Error importing row: %s', $e->getMessage()));
                }
            }
        } finally {
            fclose($file);
        }

        $io->success(sprintf('Import complete: %d imported, %d errors', $importedCount, $errorCount));

        return Command::SUCCESS;
    }
}
