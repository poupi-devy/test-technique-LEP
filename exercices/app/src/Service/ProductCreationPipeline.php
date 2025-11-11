<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ProductCreateRequest;
use App\DTO\ValidationResult;
use App\Entity\Product;
use App\Event\ProductCreatedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final readonly class ProductCreationPipeline
{
    public function __construct(
        private ValidatorWrapper $validator,
        private ProductHydrator $hydrator,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function validate(ProductCreateRequest $request): ValidationResult
    {
        return $this->validator->validate($request);
    }

    public function hydrate(ProductCreateRequest $request): Product
    {
        return $this->hydrator->hydrate($request);
    }

    public function dispatchCreatedEvent(Product $product): void
    {
        $this->eventDispatcher->dispatch(new ProductCreatedEvent($product));
    }
}