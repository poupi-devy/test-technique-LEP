<?php

declare(strict_types=1);

namespace App\EventHandler;

use App\Event\BookImportedEvent;
use App\Service\BookPersister;
use App\Service\BookValidator;
use InvalidArgumentException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: BookImportedEvent::class)]
final readonly class ValidateAndPersistBookHandler
{
    public function __construct(
        private BookValidator $validator,
        private BookPersister $persister,
    ) {
    }

    public function __invoke(BookImportedEvent $event): void
    {
        $bookData = $event->getBookData();

        $errors = $this->validator->validate($bookData);
        if (count($errors) > 0) {
            throw new InvalidArgumentException(
                sprintf('Validation failed for book "%s": %s', $bookData->title, (string) $errors)
            );
        }

        $this->persister->persist($bookData);
    }
}
