<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ProductCreateRequest;
use App\Entity\Product;
use DateTimeInterface;
use Psr\Log\LoggerInterface;

final readonly class CreateProductService
{
    public function __construct(
        private ProductCreationPipeline $pipeline,
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

        $validationResult = $this->pipeline->validate($request);

        if (!$validationResult->isValid) {
            $this->logger->warning('Product validation failed', [
                'violations_count' => count($validationResult->errors),
            ]);

            return [
                'success' => false,
                'error' => 'validation_failed',
                'violations' => $validationResult->errors,
            ];
        }

        $product = $this->pipeline->hydrate($request);
        $this->pipeline->dispatchCreatedEvent($product);

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
