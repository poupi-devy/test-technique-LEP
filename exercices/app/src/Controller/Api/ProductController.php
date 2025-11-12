<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DTO\ApiErrorResponse;
use App\DTO\ProductCreateRequest;
use App\Service\CreateProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1')]
final class ProductController extends AbstractController
{
    public function __construct(
        private readonly CreateProductService $createProductService,
    ) {
    }

    #[Route('/products', methods: ['POST'])]
    public function __invoke(
        #[MapRequestPayload]
        ProductCreateRequest $productRequest,
    ): JsonResponse {
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
