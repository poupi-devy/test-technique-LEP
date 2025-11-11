<?php

declare(strict_types=1);

namespace App\Service;

use App\Parser\FileParserInterface;

final readonly class ImportBooksService
{
    public function __construct(
        private FileParserInterface $fileParser,
        private BookImportHydrator $hydrator,
        private BookValidator $validator,
        private BookPersister $persister,
    ) {
    }

    /**
     * Import books from file
     *
     * @return array<string, int>
     */
    public function importFromFile(string $filePath): array
    {
        $importedCount = 0;
        $errorCount = 0;

        foreach ($this->fileParser->parse($filePath) as $row) {
            try {
                $bookData = $this->hydrator->hydrate($row);

                if (!$this->validator->isValid($bookData)) {
                    $errorCount++;
                    continue;
                }

                $this->persister->persist($bookData);
                $importedCount++;
            } catch (\Exception) {
                $errorCount++;
            }
        }

        return [
            'imported' => $importedCount,
            'errors' => $errorCount,
        ];
    }
}
