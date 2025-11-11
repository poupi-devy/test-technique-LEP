<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
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

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertStringContainsString('Test Product', $client->getResponse()->getContent());
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

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonStringEqualsJsonString(
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

        $this->assertResponseStatusCodeSame(422);
        $response = json_decode((string) $client->getResponse()->getContent(), true);
        $this->assertIsArray($response);
        $this->assertSame('validation_failed', $response['error'] ?? null);
        $this->assertNotEmpty($response['violations'] ?? null);
    }
}
