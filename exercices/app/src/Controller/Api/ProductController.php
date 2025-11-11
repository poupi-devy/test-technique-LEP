<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Product;
use App\Event\ProductCreatedEvent;
use DateTimeInterface;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/v1')]
final class ProductController extends AbstractController
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    #[Route('/products', methods: [Request::METHOD_POST])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = $this->decodeJsonPayload($request);

        if ($data === null) {
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

        $product = $this->hydrateProduct($data);

        $errors = $this->validator->validate($product);
        if (count($errors) > 0) {
            return $this->json([
                'error' => 'validation_failed',
                'violations' => $this->mapValidationErrors($errors),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->eventDispatcher->dispatch(new ProductCreatedEvent($product));

        $productId = $product->getId();
        if ($productId === null) {
            return $this->json([
                'error' => 'internal_error',
                'message' => 'Product was not persisted with an ID',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($this->formatProductResponse($product), Response::HTTP_CREATED, [
            'Location' => sprintf('/api/v1/products/%d', $productId),
        ]);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decodeJsonPayload(Request $request): mixed
    {
        try {
            return json_decode($request->getContent(), true);
        } catch (JsonException) {
            return null;
        }
    }

    private function hydrateProduct(array $data): Product
    {
        $product = new Product();

        if (isset($data['name']) && is_string($data['name'])) {
            $product->setName($data['name']);
        }

        if (array_key_exists('description', $data) && (is_string($data['description']) || $data['description'] === null)) {
            $product->setDescription($data['description']);
        }

        if (isset($data['price']) && is_numeric($data['price'])) {
            $product->setPrice((string) $data['price']);
        }

        if (isset($data['categoryId']) && is_int($data['categoryId'])) {
            $product->setCategoryId($data['categoryId']);
        }

        return $product;
    }

    /**
     * @return list<array<string, string>>
     */
    private function mapValidationErrors($errors): array
    {
        $violations = [];
        foreach ($errors as $error) {
            $violations[] = [
                'field' => (string) $error->getPropertyPath(),
                'message' => $error->getMessage(),
            ];
        }

        return $violations;
    }

    /**
     * @return array<string, mixed>
     */
    private function formatProductResponse(Product $product): array
    {
        return [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
            'categoryId' => $product->getCategoryId(),
            'createdAt' => $product->getCreatedAt()->format(DateTimeInterface::ATOM),
        ];
    }
}
