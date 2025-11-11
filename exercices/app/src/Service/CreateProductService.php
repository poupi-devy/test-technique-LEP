<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ProductCreateRequest;
use App\Entity\Product;
use App\Event\ProductCreatedEvent;
use DateTimeInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class CreateProductService
{
    public function __construct(
        private ValidatorInterface $validator,
        private EventDispatcherInterface $eventDispatcher,
        private ValidationErrorFormatter $errorFormatter,
        private ProductHydrator $hydrator,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function handle(ProductCreateRequest $request): array
    {
        $this->logger->info('Creating product', [
            'name' => $request->name,
            'price' => $request->price,
            'categoryId' => $request->categoryId,
        ]);

        $violations = $this->validator->validate($request);

        if (count($violations) > 0) {
            $this->logger->warning('Product validation failed', [
                'violations_count' => count($violations),
            ]);

            return [
                'success' => false,
                'error' => 'validation_failed',
                'violations' => $this->errorFormatter->format($violations),
            ];
        }

        $product = $this->hydrator->hydrate($request);

        $this->eventDispatcher->dispatch(new ProductCreatedEvent($product));

        $this->logger->info('Product created successfully', [
            'product_id' => $product->getId(),
            'name' => $product->getName(),
        ]);

        return [
            'success' => true,
            'product' => $this->formatProductResponse($product),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function formatProductResponse(Product $product): array
    {
        return [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
            'categoryId' => $product->getCategoryId(),
            'createdAt' => $product->getCreatedAt()->format(DateTimeInterface::ATOM),
        ];
    }
}
