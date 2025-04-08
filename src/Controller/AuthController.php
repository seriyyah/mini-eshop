<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

class AuthController extends AbstractController
{
    #[OA\Post(
        path: '/api/login_check',
        description: 'Generate a JWT token.',
        summary: 'Get a JWT token',
        requestBody: new OA\RequestBody(
            description: 'User credentials in JSON format',
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    required: ['email', 'password'],
                    properties: [
                        new OA\Property(
                            property: 'email',
                            type: 'string',
                            example: 'user@example.com'
                        ),
                        new OA\Property(
                            property: 'password',
                            type: 'string',
                            example: 'your_password'
                        ),
                    ],
                    type: 'object'
                )
            )
        ),
        tags: ['Authentication'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'JWT token returned upon successful authentication.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'token',
                            type: 'string',
                            example: 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.ey...'
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Invalid credentials'
            )
        ]
    )]
    #[Route('/api/login_check', name: 'app_login_check', methods: ['POST'])]
    public function login(): JsonResponse
    {
        throw new \LogicException('This endpoint is documented for Swagger');
    }
}