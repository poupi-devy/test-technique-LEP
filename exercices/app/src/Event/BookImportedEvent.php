<?php

declare(strict_types=1);

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * @template T of array<string, int|string>
 */
final class BookImportedEvent extends Event
{
    /**
     * @param array<string, int|string> $data
     */
    public function __construct(
        private readonly array $data,
    ) {
    }

    /**
     * @return array<string, int|string>
     */
    public function getData(): array
    {
        return $this->data;
    }
}
