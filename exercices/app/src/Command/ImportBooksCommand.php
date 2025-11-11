<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ImportBooksService;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import:books',
    description: 'Import books from a CSV file into the database',
)]
final readonly class ImportBooksCommand
{
    public function __construct(private ImportBooksService $importService)
    {
    }

    public function __invoke(#[Argument(description: 'Path to the CSV file containing book data', name: 'file')]
        string $filePath, SymfonyStyle $io): int
    {
        if (!file_exists($filePath)) {
            $io->error(sprintf('File not found: %s', $filePath));
            return Command::FAILURE;
        }

        $io->info(sprintf('Starting import from file: %s', $filePath));

        try {
            $result = $this->importService->importFromFile($filePath);

            $io->success(sprintf(
                'Import complete: %d imported, %d errors',
                $result['imported'],
                $result['errors']
            ));

            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $io->error(sprintf('Import failed: %s', $exception->getMessage()));
            return Command::FAILURE;
        }
    }
}
