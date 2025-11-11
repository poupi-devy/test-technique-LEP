<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class ProductCreateRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 255)]
        public string $name,
        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public string $price,
        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $categoryId,
        #[Assert\Length(max: 5000)]
        public ?string $description = null,
    ) {
    }
}
