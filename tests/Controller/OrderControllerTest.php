<?php

namespace App\Tests\Controller;

use App\Tests\WebTestCaseHelperTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class OrderControllerTest extends WebTestCase
{
    use WebTestCaseHelperTrait;

    /**
     * @throws \JsonException
     */
    public function testCreateOrderEndpoint(): void
    {
        $client = static::createClient();

        $this->login();

        $payload = [
            'userId' => 1,
            'items' => [
                [
                    'productName' => 'Keyboard',
                    'unitPrice' => 45.50,
                    'quantity' => 1,
                ]
            ]
        ];

        $this->post(
            '/api/orders',
            $payload
        );

        self::assertResponseStatusCodeSame(201);

        $responseData = json_decode(
            $client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $this->assertArrayHasKey('id', $responseData);

        $this->assertArrayHasKey('status', $responseData);
    }

    public function testGetOrderNotFound(): void
    {
        $client = static::createClient();

        $this->login();

        $client->request('GET', '/api/orders/99999');

        self::assertResponseStatusCodeSame(404);
    }

    /**
     * @throws \JsonException
     */
    public function testListOrdersEndpoint(): void
    {
        $client = static::createClient();

        $this->login();

        $this->get('/api/orders');

        self::assertResponseIsSuccessful();

        $responseData = json_decode(
            $client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        $this->assertIsArray($responseData);
    }
}