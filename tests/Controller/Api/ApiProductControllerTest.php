<?php

namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiProductControllerTest extends WebTestCase
{
    public function testApiProductsRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/products');

        // Should return 401 without JWT token
        $this->assertResponseStatusCodeSame(401);
    }

    public function testApiLoginWithValidRoute(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'username' => 'nonexistent@test.com',
            'password' => 'wrongpassword',
        ]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testApiLoginWithMissingCredentials(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([]));

        // Should return 400 for missing credentials
        $this->assertResponseStatusCodeSame(400);
    }

    public function testApiLoginReturnsJson(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'username' => 'test@test.com',
            'password' => 'wrong',
        ]));

        $response = $client->getResponse();

        $this->assertSame('application/json', $response->headers->get('Content-Type'));
    }
}
