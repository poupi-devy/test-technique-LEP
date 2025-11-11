<?php

namespace App\Event;

use App\Entity\Product;
use Symfony\Contracts\EventDispatcher\Event;

final class ProductCreatedEvent extends Event
{
    public function __construct(
        private readonly Product $product,
    ) {
    }

    public function getProduct(): Product
    {
        return $this->product;
    }
}