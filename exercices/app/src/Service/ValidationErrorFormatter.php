<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Validator\ConstraintViolationListInterface;

final class ValidationErrorFormatter
{
    /**
     * @return list<array{field: string, message: string|object}>
     */
    public function format(ConstraintViolationListInterface $violations): array
    {
        $errors = [];
        foreach ($violations as $violation) {
            $errors[] = [
                'field' => $violation->getPropertyPath(),
                'message' => $violation->getMessage(),
            ];
        }

        return $errors;
    }
}
