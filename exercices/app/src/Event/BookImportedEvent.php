<?php

declare(strict_types=1);

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class BookImportedEvent extends Event
{
    public function __construct(
        private readonly array $data,
    ) {
    }

    public function getData(): array
    {
        return $this->data;
    }
}
