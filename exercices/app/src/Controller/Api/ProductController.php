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

        $request = new ProductCreateRequest(
            name: $data['name'] ?? '',
            description: $data['description'] ?? null,
            price: (string) ($data['price'] ?? ''),
            categoryId: $data['categoryId'] ?? 0,
        );

        $result = $this->createProductService->handle($request);

        if (!$result['success']) {
            return $this->json([
                'error' => $result['error'],
                'violations' => $result['violations'],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $product = $result['product'];

        return $this->json($product, Response::HTTP_CREATED, [
            'Location' => sprintf('/api/v1/products/%d', $product['id']),
        ]);
    }
}
