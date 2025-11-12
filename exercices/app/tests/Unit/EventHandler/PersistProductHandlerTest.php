<?php

declare(strict_types=1);

namespace App\Tests\Unit\EventHandler;

use App\Entity\Product;
use App\Event\ProductCreatedEvent;
use App\EventHandler\PersistProductHandler;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class PersistProductHandlerTest extends TestCase
{
    private PersistProductHandler $handler;

    private \PHPUnit\Framework\MockObject\MockObject $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->handler = new PersistProductHandler($this->entityManager);
    }

    public function testHandleInvokesPersistAndFlush(): void
    {
        $product = new Product();
        $product->setName('Test Product');
        $product->setPrice('99.99');
        $product->setCategoryId(1);

        $event = new ProductCreatedEvent($product);

        $this->entityManager
            ->expects(self::once())
            ->method('persist');

        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        ($this->handler)($event);
    }

    public function testHandlePersistsCorrectProduct(): void
    {
        $product = new Product();
        $product->setName('Test Product');
        $product->setPrice('99.99');
        $product->setCategoryId(1);

        $event = new ProductCreatedEvent($product);
        $persistedProducts = [];

        $this->entityManager
            ->method('persist')
            ->willReturnCallback(function ($entity) use (&$persistedProducts): void {
                $persistedProducts[] = $entity;
            });

        ($this->handler)($event);

        self::assertCount(1, $persistedProducts);
        self::assertSame($product, $persistedProducts[0]);
    }

    public function testHandleFlushIsCalledAfterPersist(): void
    {
        $product = new Product();
        $product->setName('Product');
        $product->setPrice('50.00');
        $product->setCategoryId(1);

        $event = new ProductCreatedEvent($product);
        $callOrder = [];

        $this->entityManager
            ->method('persist')
            ->willReturnCallback(function () use (&$callOrder): void {
                $callOrder[] = 'persist';
            });

        $this->entityManager
            ->method('flush')
            ->willReturnCallback(function () use (&$callOrder): void {
                $callOrder[] = 'flush';
            });

        ($this->handler)($event);

        self::assertSame(['persist', 'flush'], $callOrder);
    }
}
