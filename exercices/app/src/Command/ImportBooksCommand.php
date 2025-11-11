<?php

declare(strict_types=1);

namespace App\Command;

use App\Event\BookImportedEvent;
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
    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function __invoke(#[\Symfony\Component\Console\Attribute\Argument(name: 'file', description: 'Path to the CSV file containing book data')]
        string $file, \Symfony\Component\Console\Style\SymfonyStyle $io): int
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
                if (count($row) < 4) {
                    $errorCount++;
                    $io->warning(sprintf('Invalid row (expecting 4 columns): %s', implode(',', $row)));
                    continue;
                }

                try {
                    $data = [
                        'title' => $row[0] ?? '',
                        'author' => $row[1] ?? '',
                        'year' => (int) ($row[2] ?? 0),
                        'isbn' => $row[3] ?? '',
                    ];

                    $event = new BookImportedEvent($data);
                    $this->eventDispatcher->dispatch($event);
                    $importedCount++;

                    $io->writeln(sprintf('✓ Imported: %s', $data['title']));
                } catch (\Exception $e) {
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
