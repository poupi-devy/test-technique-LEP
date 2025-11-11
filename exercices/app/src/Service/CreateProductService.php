<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ProductCreateRequest;
use App\Entity\Product;
use App\Event\ProductCreatedEvent;
use DateTimeInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CreateProductService
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ValidationErrorFormatter $errorFormatter,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function handle(ProductCreateRequest $request): array
    {
        $violations = $this->validator->validate($request);

        if (count($violations) > 0) {
            return [
                'success' => false,
                'error' => 'validation_failed',
                'violations' => $this->errorFormatter->format($violations),
            ];
        }

        $product = $this->hydrateProduct($request);

        $this->eventDispatcher->dispatch(new ProductCreatedEvent($product));

        return [
            'success' => true,
            'product' => $this->formatProductResponse($product),
        ];
    }

    private function hydrateProduct(ProductCreateRequest $request): Product
    {
        $product = new Product();
        $product->setName($request->name);
        $product->setDescription($request->description);
        $product->setPrice($request->price);
        $product->setCategoryId($request->categoryId);

        return $product;
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
