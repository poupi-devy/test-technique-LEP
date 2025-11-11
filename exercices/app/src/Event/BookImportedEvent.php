<?php

declare(strict_types=1);

namespace App\Event;

use App\DTO\BookImportData;
use Symfony\Contracts\EventDispatcher\Event;

final class BookImportedEvent extends Event
{
    public function __construct(
        private readonly BookImportData $bookData,
    ) {
    }

    public function getBookData(): BookImportData
    {
        return $this->bookData;
    }
}
