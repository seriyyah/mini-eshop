<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class RegisterControllerTest extends WebTestCase
{
    public function testRegisterUserEndpoint(): void
    {
        $client = static::createClient();

        $payload = [
            'email' => 'test@example.com',
            'password' => 'secret123'
        ];

        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $this->assertResponseStatusCodeSame(201);

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertSame('User created', $responseData['message']);
    }
}