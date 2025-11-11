<?php

declare(strict_types=1);

namespace App\Service;

use App\Parser\FileParserInterface;
use Psr\Log\LoggerInterface;

final readonly class ImportBooksService
{
    public function __construct(
        private FileParserInterface $fileParser,
        private BookImportHydrator $hydrator,
        private BookValidator $validator,
        private BookPersister $persister,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Import books from file
     *
     * @return array<string, int>
     */
    public function importFromFile(string $filePath): array
    {
        $this->logger->info('Starting book import', ['file' => $filePath]);

        $importedCount = 0;
        $errorCount = 0;
        $rowIndex = 0;

        foreach ($this->fileParser->parse($filePath) as $row) {
            ++$rowIndex;

            try {
                $bookData = $this->hydrator->hydrate($row);
                $this->logger->debug('Hydrated book data', ['row' => $rowIndex, 'isbn' => $bookData->isbn]);

                if (!$this->validator->isValid($bookData)) {
                    $this->logger->warning('Book validation failed', [
                        'row' => $rowIndex,
                        'isbn' => $bookData->isbn,
                    ]);
                    ++$errorCount;
                    continue;
                }

                $this->persister->persist($bookData);
                ++$importedCount;
                $this->logger->info('Book persisted successfully', ['row' => $rowIndex, 'isbn' => $bookData->isbn]);
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
