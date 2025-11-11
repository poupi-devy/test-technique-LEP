<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class BookImportData
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 255)]
        public string $title,
        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 255)]
        public string $author,
        #[Assert\NotBlank]
        #[Assert\GreaterThan(1000)]
        public int $year,
        #[Assert\NotBlank]
        #[Assert\Isbn]
        public string $isbn,
    ) {
    }
}
