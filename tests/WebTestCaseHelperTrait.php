<?php

namespace App\Tests;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\Request;

use function json_encode;

trait WebTestCaseHelperTrait
{
    protected static function getClient(?AbstractBrowser $newClient = null): ?AbstractBrowser
    {
        $method = new ReflectionMethod(WebTestCase::class, 'getClient');

        /** @var KernelBrowser $client */
        $client = $method->invoke(null);

        return $client;
    }

    /**
     * @param array<string, mixed> $body
     * @throws \JsonException
     */
    public function post(string $uri, array $body = []): KernelBrowser
    {
        /** @var string $json */
        $json = json_encode($body, JSON_THROW_ON_ERROR);

        $client = self::getClient();
        $client->request(
            Request::METHOD_POST,
            $uri,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $json
        );

        return $client;
    }

    /**
     * @param array<string, mixed> $body
     * @throws \JsonException
     */
    public function put(string $uri, array $body = []): void
    {
        /** @var string $json */
        $json = json_encode($body, JSON_THROW_ON_ERROR);

        self::getClient()->request(
            Request::METHOD_PUT,
            $uri,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $json
        );
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function get(string $uri, array $parameters = []): void
    {
        self::getClient()->request(
            Request::METHOD_GET,
            sprintf('%s?%s', $uri, http_build_query($parameters))
        );
    }

    public function delete(string $uri): void
    {
        self::getClient()->request(Request::METHOD_DELETE, $uri);
    }

    public function login(string $apiKey = 'api-key-1'): void
    {
        $client = static::getClient();
        $token = $this->generateToken();
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $token));
    }

    public function generateToken(): string
    {
        $container = static::getContainer();

        $userRepository = $container->get(\Doctrine\Persistence\ManagerRegistry::class)
            ->getRepository(\App\Entity\User::class);
        $user = $userRepository->findOneBy(['email' => 'user+1@example.com']);

        /** @var JWTTokenManagerInterface $jwtManager */
        $jwtManager = $container->get(JWTTokenManagerInterface::class);
        return $jwtManager->create($user);
    }
}