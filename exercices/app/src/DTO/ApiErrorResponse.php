<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class ApiErrorResponse
{
    /**
     * @param array<int, array{field: string, message: string|object}> $violations
     */
    public function __construct(
        public string $error,
        public string $message,
        public array $violations = [],
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $result = [
            'error' => $this->error,
            'message' => $this->message,
        ];

        if ($this->violations !== []) {
            $result['violations'] = $this->violations;
        }

        return $result;
    }
}
