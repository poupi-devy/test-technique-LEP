<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ProductCreateRequest;
use App\Entity\Product;

final class ProductHydrator
{
    public function hydrate(ProductCreateRequest $request): Product
    {
        $product = new Product();
        $product->setName($request->name);
        $product->setDescription($request->description);
        $product->setPrice($request->price);
        $product->setCategoryId($request->categoryId);

        return $product;
    }
}
