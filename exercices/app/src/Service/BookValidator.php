<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\BookImportData;
use App\DTO\ValidationResult;

final readonly class BookValidator
{
    public function __construct(
        private ValidatorWrapper $validator,
    ) {
    }

    public function validate(BookImportData $bookData): ValidationResult
    {
        return $this->validator->validate($bookData);
    }

    public function isValid(BookImportData $bookData): bool
    {
        return $this->validator->isValid($bookData);
    }
}
