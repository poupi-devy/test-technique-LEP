<?php

declare(strict_types=1);

namespace App\EventHandler;

use App\Event\BookImportedEvent;
use App\Service\BookPersister;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: BookImportedEvent::class)]
final readonly class PersistBookHandler
{
    public function __construct(
        private BookPersister $persister,
    ) {
    }

    public function __invoke(BookImportedEvent $event): void
    {
        $bookData = $event->getBookData();
        $this->persister->persist($bookData);
    }
}
