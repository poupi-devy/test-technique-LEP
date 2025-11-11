<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\BookImportData;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class BookValidator
{
    public function __construct(
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function validate(BookImportData $bookData): ConstraintViolationListInterface
    {
        return $this->validator->validate($bookData);
    }

    public function isValid(BookImportData $bookData): bool
    {
        return count($this->validator->validate($bookData)) === 0;
    }
}