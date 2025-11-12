<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;

final readonly class ImportBooksService
{
    public function __construct(
        private BookImportPipeline $pipeline,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @return array<string, int>
     */
    public function importFromFile(string $filePath): array
    {
        $this->logger->info('Starting book import', ['file' => $filePath]);

        $importedCount = 0;
        $errorCount = 0;
        $rowIndex = 0;

        foreach ($this->pipeline->getFileParser()->parse($filePath) as $row) {
            ++$rowIndex;

            try {
                $bookData = $this->pipeline->hydrate($row);
                $this->logger->debug('Hydrated book data', ['row' => $rowIndex, 'isbn' => $bookData->isbn]);

                if (!$this->pipeline->validate($bookData)) {
                    $this->logger->warning('Book validation failed', [
                        'row' => $rowIndex,
                        'isbn' => $bookData->isbn,
                    ]);
                    ++$errorCount;
                    continue;
                }

                $this->pipeline->dispatchBookImportedEvent($bookData);
                ++$importedCount;
                $this->logger->info('Book imported successfully', ['row' => $rowIndex, 'isbn' => $bookData->isbn]);
            } catch (\Exception $exception) {
                ++$errorCount;
                $this->logger->error('Book import error', [
                    'row' => $rowIndex,
                    'error' => $exception->getMessage(),
                    'exception' => $exception,
                ]);
            }
        }

        $this->logger->info('Book import completed', [
            'imported' => $importedCount,
            'errors' => $errorCount,
            'file' => $filePath,
        ]);

        return [
            'imported' => $importedCount,
            'errors' => $errorCount,
        ];
    }
}
