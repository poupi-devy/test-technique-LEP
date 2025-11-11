<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\BookImportData;
use App\Parser\FileParserInterface;

final readonly class BookImportPipeline
{
    public function __construct(
        private FileParserInterface $fileParser,
        private BookImportHydrator $hydrator,
        private ValidatorWrapper $validator,
        private BookPersister $persister,
    ) {
    }

    public function getFileParser(): FileParserInterface
    {
        return $this->fileParser;
    }

    public function hydrate(array $row): BookImportData
    {
        return $this->hydrator->hydrate($row);
    }

    public function validate(BookImportData $bookData): bool
    {
        return $this->validator->isValid($bookData);
    }

    public function persist(BookImportData $bookData): void
    {
        $this->persister->persist($bookData);
    }
}