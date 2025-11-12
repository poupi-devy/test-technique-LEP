<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ValidationResult;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class ValidatorWrapper
{
    public function __construct(
        private ValidatorInterface $validator,
        private ValidationErrorFormatter $errorFormatter,
    ) {
    }

    public function validate(object $data): ValidationResult
    {
        $violations = $this->validator->validate($data);
        $isValid = count($violations) === 0;

        return new ValidationResult(
            isValid: $isValid,
            errors: $isValid ? [] : $this->errorFormatter->format($violations),
        );
    }

    public function isValid(object $data): bool
    {
        return count($this->validator->validate($data)) === 0;
    }
}
