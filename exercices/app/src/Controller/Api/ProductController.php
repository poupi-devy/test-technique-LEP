<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DTO\ApiErrorResponse;
use App\DTO\ProductCreateRequest;
use App\Service\CreateProductService;
use JsonException;
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

    #[Route('/products', methods: [Request::METHOD_POST])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = $this->parseJsonRequest($request);
        if ($data instanceof JsonResponse) {
            return $data;
        }

        $name = is_string($data['name'] ?? null) ? $data['name'] : '';
        $price = is_numeric($data['price'] ?? null) ? (string) $data['price'] : '';
        $categoryId = is_int($data['categoryId'] ?? null) ? $data['categoryId'] : 0;
        $description = is_string($data['description'] ?? null) ? $data['description'] : null;

        $productRequest = new ProductCreateRequest(
            name: $name,
            price: $price,
            categoryId: $categoryId,
            description: $description,
        );

        $result = $this->createProductService->handle($productRequest);

        if (!($result['success'] ?? false)) {
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

        if (!is_array($product)) {
            $errorResponse = new ApiErrorResponse(
                error: 'internal_error',
                message: 'Invalid product response from service',
            );

            return $this->json(
                $errorResponse->toArray(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        $productId = is_int($product['id'] ?? null) ? $product['id'] : 0;

        return $this->json($product, Response::HTTP_CREATED, [
            'Location' => sprintf('/api/v1/products/%d', $productId),
        ]);
    }

    /**
     * @return array<string, mixed>|JsonResponse
     */
    private function parseJsonRequest(Request $request): array|JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
        } catch (JsonException) {
            $errorResponse = new ApiErrorResponse(
                error: 'invalid_request',
                message: 'Request body must be valid JSON',
            );

            return $this->json(
                $errorResponse->toArray(),
                Response::HTTP_BAD_REQUEST
            );
        }

        if (!is_array($data)) {
            $errorResponse = new ApiErrorResponse(
                error: 'invalid_request',
                message: 'Request body must be a JSON object',
            );

            return $this->json(
                $errorResponse->toArray(),
                Response::HTTP_BAD_REQUEST
            );
        }

        return $data;
    }
}
