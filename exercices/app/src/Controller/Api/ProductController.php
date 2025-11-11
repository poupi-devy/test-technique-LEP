<?php

declare(strict_types=1);

namespace App\Controller\Api;

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
        try {
            $data = json_decode($request->getContent(), true);
        } catch (JsonException) {
            return $this->json([
                'error' => 'invalid_request',
                'message' => 'Invalid JSON payload',
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!is_array($data)) {
            return $this->json([
                'error' => 'invalid_request',
                'message' => 'Request body must be a JSON object',
            ], Response::HTTP_BAD_REQUEST);
        }

        $name = is_string($data['name'] ?? null) ? $data['name'] : '';
        $price = is_numeric($data['price'] ?? null) ? (string) $data['price'] : '';
        $categoryId = is_int($data['categoryId'] ?? null) ? $data['categoryId'] : 0;
        $description = is_string($data['description'] ?? null) ? $data['description'] : null;

        $request = new ProductCreateRequest(
            name: $name,
            price: $price,
            categoryId: $categoryId,
            description: $description,
        );

        $result = $this->createProductService->handle($request);

        if (!$result['success']) {
            return $this->json([
                'error' => $result['error'],
                'violations' => $result['violations'],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $product = $result['product'];
        $productId = is_int($product['id'] ?? null) ? $product['id'] : 0;

        return $this->json($product, Response::HTTP_CREATED, [
            'Location' => sprintf('/api/v1/products/%d', $productId),
        ]);
    }
}
