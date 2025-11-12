<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DTO\ApiErrorResponse;
use App\DTO\ProductCreateRequest;
use App\Service\CreateProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1')]
final class ProductController extends AbstractController
{
    public function __construct(
        private readonly CreateProductService $createProductService,
    ) {
    }

    #[Route('/products', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            /** @var mixed $decoded */
            $decoded = json_decode((string) $request->getContent(), true, flags: JSON_THROW_ON_ERROR);

            if (!is_array($decoded)) {
                return $this->json(
                    ['error' => 'invalid_request'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            /** @var array<string, mixed> $data */
            $data = $decoded;
        } catch (\JsonException) {
            return $this->json(
                ['error' => 'invalid_request'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $nameValue = $data['name'] ?? '';
        $name = is_string($nameValue) ? $nameValue : '';

        $priceValue = $data['price'] ?? '';
        $price = match (true) {
            is_string($priceValue) => $priceValue,
            is_int($priceValue), is_float($priceValue) => (string) $priceValue,
            default => '',
        };

        /** @var mixed $categoryIdValue */
        $categoryIdValue = $data['categoryId'] ?? 0;
        $categoryId = match (true) {
            is_int($categoryIdValue) => $categoryIdValue,
            /** @phpstan-ignore-next-line cast.int */
            default => (int) $categoryIdValue,
        };

        $descriptionValue = $data['description'] ?? null;
        $description = is_string($descriptionValue) ? $descriptionValue : null;

        $productRequest = new ProductCreateRequest(
            name: $name,
            price: $price,
            categoryId: $categoryId,
            description: $description,
        );

        /** @var array{success: bool, error?: string, violations?: list<array{field: string, message: object|string}>, product?: array<string, mixed>} $result */
        $result = $this->createProductService->handle($productRequest);

        if (!$result['success']) {
            $errorResponse = new ApiErrorResponse(
                error: $result['error'] ?? 'unknown_error',
                message: 'Product creation failed. Please check the violations below.',
                violations: $result['violations'] ?? [],
            );

            return $this->json(
                $errorResponse->toArray(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $product = $result['product'] ?? [];

        $productId = is_int($product['id'] ?? null) ? $product['id'] : 0;

        return $this->json($product, Response::HTTP_CREATED, [
            'Location' => sprintf('/api/v1/products/%d', $productId),
        ]);
    }
}
