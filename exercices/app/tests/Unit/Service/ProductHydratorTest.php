<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\DTO\ProductCreateRequest;
use App\Entity\Product;
use App\Service\ProductHydrator;
use PHPUnit\Framework\TestCase;

final class ProductHydratorTest extends TestCase
{
    private ProductHydrator $hydrator;

    protected function setUp(): void
    {
        $this->hydrator = new ProductHydrator();
    }

    public function testHydrateWithValidData(): void
    {
        $request = new ProductCreateRequest(
            name: 'Test Product',
            price: '99.99',
            categoryId: 1,
            description: 'A test product',
        );

        $product = $this->hydrator->hydrate($request);

        self::assertInstanceOf(Product::class, $product);
        self::assertSame('Test Product', $product->getName());
        self::assertSame('99.99', $product->getPrice());
        self::assertSame(1, $product->getCategoryId());
        self::assertSame('A test product', $product->getDescription());
    }

    public function testHydrateWithoutDescription(): void
    {
        $request = new ProductCreateRequest(
            name: 'Product',
            price: '50.00',
            categoryId: 2,
        );

        $product = $this->hydrator->hydrate($request);

        self::assertNull($product->getDescription());
        self::assertSame('Product', $product->getName());
        self::assertSame('50.00', $product->getPrice());
        self::assertSame(2, $product->getCategoryId());
    }

    public function testHydrateCreatedAtIsSet(): void
    {
        $request = new ProductCreateRequest(
            name: 'Product',
            price: '10.00',
            categoryId: 1,
        );

        $product = $this->hydrator->hydrate($request);

        self::assertNotNull($product->getCreatedAt());
    }

    public function testHydrateWithLongDescription(): void
    {
        $longDescription = str_repeat('a', 5000);
        $request = new ProductCreateRequest(
            name: 'Product',
            price: '100.00',
            categoryId: 1,
            description: $longDescription,
        );

        $product = $this->hydrator->hydrate($request);

        self::assertSame($longDescription, $product->getDescription());
    }

    public function testHydratePreservesExactPrice(): void
    {
        $request = new ProductCreateRequest(
            name: 'Product',
            price: '0.01',
            categoryId: 1,
        );

        $product = $this->hydrator->hydrate($request);

        self::assertSame('0.01', $product->getPrice());
    }
}
