<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class ValidationResult
{
    /**
     * @param list<array{field: string, message: string|object}> $errors
     */
    public function __construct(
        public bool $isValid,
        public array $errors = [],
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'isValid' => $this->isValid,
            'errors' => $this->errors,
        ];
    }
}
