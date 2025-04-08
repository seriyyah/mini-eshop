<?php

namespace App\Controller;

use App\DTO\RegisterRequest;
use App\Service\RegisterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;

#[OA\Post(
    path: '/api/register',
    summary: 'Register a new user',
    requestBody: new OA\RequestBody(
        description: 'User registration payload',
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
                    )
                ],
                type: 'object'
            )
        )
    ),
    tags: ['Authentication'],
    responses: [
        new OA\Response(
            response: 201,
            description: 'User registered successfully',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: 'User created'
                    )
                ]
            )
        ),
        new OA\Response(response: 400, description: 'Invalid input')
    ]
)]
#[Route('/api/register', name: 'app_register', methods: ['POST'])]
final class RegisterController extends AbstractController
{
    public function __construct(
       private readonly RegisterService $registerService,
       private readonly SerializerInterface $serializer,
       private readonly ValidatorInterface $validator
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = $this->serializer->deserialize($request->getContent(), RegisterRequest::class, 'json');

        $errors = $this->validator->validate($data);

        if ($errors->count() > 0) {
            return new JsonResponse(
                $this->serializer->serialize($errors, 'json'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $this->registerService->registerUser($data->email, $data->password);

        return new JsonResponse(['message' => 'User created'], JsonResponse::HTTP_CREATED);
    }
}