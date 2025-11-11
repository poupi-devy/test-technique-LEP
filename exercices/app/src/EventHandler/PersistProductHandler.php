<?php

namespace App\EventHandler;

use App\Event\ProductCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: ProductCreatedEvent::class)]
final class PersistProductHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(ProductCreatedEvent $event): void
    {
        $product = $event->getProduct();
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }
}