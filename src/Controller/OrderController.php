<?php

namespace App\Controller;

use App\DTO\CreateOrderRequest;
use App\Entity\Order;
use App\Service\OrderService;
use Nelmio\ApiDocBundle\Attribute\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

final class OrderController extends AbstractController
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator
    ) {
    }

    #[Route('/api/orders', name: 'create_order', methods: ['POST'])]
    #[OA\Post(
        path: '/api/orders',
        summary: 'Create a new order',
        requestBody: new OA\RequestBody(
            description: 'Order payload using form data',
            required: true,
            content: [
                new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        required: ['userId', 'items'],
                        type: 'object'
                    )
                )
            ]
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Order created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'status', type: 'string', example: 'NEW')
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Invalid input')
        ]
    )]
    #[Security(name: 'Bearer')]
    public function create(Request $request): JsonResponse
    {
        $data = $this->serializer->deserialize(
            $request->getContent(),
            CreateOrderRequest::class,
            'json'
        );

        $errors = $this->validator->validate($data);

        if ($errors->count() > 0) {
            return new JsonResponse(
                $this->serializer->serialize($errors, 'json'),
                JsonResponse::HTTP_BAD_REQUEST,
                [],
                true
            );
        }

        try {
            $order = $this->orderService->createOrder($data->userId, $data->items);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(
                [
                    'error' => $e->getMessage()
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return new JsonResponse(
            [
                'id' => $order->getId(),
                'status' => $order->getStatus()->value
            ],
            JsonResponse::HTTP_CREATED
        );
    }

    #[Route('/api/orders/{id}', name: 'get_order', methods: ['GET'])]
    #[OA\Get(
        path: '/api/orders/{id}',
        summary: 'Retrieve an order by ID',
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Order ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Order retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'customerEmail', type: 'string', example: 'user@example.com'),
                        new OA\Property(property: 'status', type: 'string', example: 'NEW'),
                        new OA\Property(property: 'totalPrice', type: 'number', format: 'float', example: 85.50),
                        new OA\Property(property: 'createdAt', type: 'string', format: 'date-time', example: '2025-03-10T09:30:00Z'),
                        new OA\Property(
                            property: 'items',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'productName', type: 'string', example: 'Keyboard'),
                                    new OA\Property(property: 'unitPrice', type: 'number', format: 'float', example: 45.5),
                                    new OA\Property(property: 'quantity', type: 'integer', example: 1)
                                ]
                            )
                        )
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Order not found')
        ]
    )]
    #[Security(name: 'Bearer')]
    public function get(?Order $order): JsonResponse
    {
        if (!$order) {
            return new JsonResponse(['error' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(
            $this->orderService->fetchOrder($order),
        );
    }

    #[Route('/api/orders', name: 'list_orders', methods: ['GET'])]
    #[OA\Get(
        path: '/api/orders',
        summary: 'List all orders',
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of orders',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'customerEmail', type: 'string', example: 'user@example.com'),
                            new OA\Property(property: 'status', type: 'string', example: 'NEW'),
                            new OA\Property(property: 'totalPrice', type: 'number', format: 'float', example: 85.50),
                            new OA\Property(property: 'createdAt', type: 'string', format: 'date-time', example: '2025-03-10T09:30:00Z'),
                            new OA\Property(
                                property: 'items',
                                type: 'array',
                                items: new OA\Items(
                                    properties: [
                                        new OA\Property(property: 'productName', type: 'string', example: 'Keyboard'),
                                        new OA\Property(property: 'unitPrice', type: 'number', format: 'float', example: 45.5),
                                        new OA\Property(property: 'quantity', type: 'integer', example: 1)
                                    ]
                                )
                            )
                        ]
                    )
                )
            )
        ]
    )]
    #[Security(name: 'Bearer')]
    public function list(Request $request): JsonResponse
    {
        return new JsonResponse($this->orderService->fetchAllOrders(
            $request->query->getInt('page', 1)
        ));
    }
}