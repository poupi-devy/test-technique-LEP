<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ProductControllerTest extends WebTestCase
{
    public function testCreateProductWithValidData(): void
    {
        $client = self::createClient();

        $client->request(
            'POST',
            '/api/v1/products',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'Test Product',
                'description' => 'Test Description',
                'price' => 99.99,
                'categoryId' => 1,
            ])
        );

        self::assertResponseStatusCodeSame(201);
        self::assertResponseHeaderSame('Content-Type', 'application/json');
        self::assertStringContainsString('Test Product', $client->getResponse()->getContent());
    }

    public function testCreateProductWithInvalidJson(): void
    {
        $client = self::createClient();

        $client->request(
            'POST',
            '/api/v1/products',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            'invalid json'
        );

        self::assertResponseStatusCodeSame(400);
        self::assertJsonStringEqualsJsonString(
            json_encode(['error' => 'invalid_request']),
            $client->getResponse()->getContent()
        );
    }

    public function testCreateProductWithValidationError(): void
    {
        $client = self::createClient();

        $client->request(
            'POST',
            '/api/v1/products',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'X',
                'price' => -10,
                'categoryId' => 0,
            ])
        );

        self::assertResponseStatusCodeSame(422);
        $response = json_decode((string) $client->getResponse()->getContent(), true);
        self::assertIsArray($response);
        self::assertSame('validation_failed', $response['error'] ?? null);
        self::assertNotEmpty($response['violations'] ?? null);
    }
}
