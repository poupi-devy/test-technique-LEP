<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Product;
use App\Event\ProductCreatedEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/products', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
        } catch (\JsonException) {
            return $this->json([
                'error' => 'invalid_request',
                'message' => 'Invalid JSON payload',
            ], 400);
        }

        if (!is_array($data)) {
            return $this->json([
                'error' => 'invalid_request',
                'message' => 'Request body must be a JSON object',
            ], 400);
        }

        $product = new Product();
        if (isset($data['name']) && is_string($data['name'])) {
            $product->setName($data['name']);
        }

        if (array_key_exists('description', $data)) {
            if (is_string($data['description'])) {
                $product->setDescription($data['description']);
            } elseif ($data['description'] === null) {
                $product->setDescription(null);
            }
        }

        if (isset($data['price']) && is_numeric($data['price'])) {
            $product->setPrice((float) $data['price']);
        }

        if (isset($data['categoryId']) && is_int($data['categoryId'])) {
            $product->setCategoryId($data['categoryId']);
        }

        $errors = $this->validator->validate($product);
        if (count($errors) > 0) {
            $violations = [];
            foreach ($errors as $error) {
                $violations[] = [
                    'field' => $error->getPropertyPath(),
                    'message' => $error->getMessage(),
                ];
            }

            return $this->json([
                'error' => 'validation_failed',
                'violations' => $violations,
            ], 422);
        }

        $event = new ProductCreatedEvent($product);
        $this->eventDispatcher->dispatch($event);

        $productId = $product->getId();
        if ($productId === null) {
            return $this->json([
                'error' => 'internal_error',
                'message' => 'Product was not persisted with an ID',
            ], 500);
        }

        return $this->json([
            'id' => $productId,
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
            'categoryId' => $product->getCategoryId(),
            'createdAt' => $product->getCreatedAt()->format(\DateTimeInterface::ATOM),
        ], 201, [
            'Location' => sprintf('/api/v1/products/%d', $productId),
        ]);
    }
}
