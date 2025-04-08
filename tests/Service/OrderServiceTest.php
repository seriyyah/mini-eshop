<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\DTO\CreateOrderRequest;
use App\DTO\OrderItemDTO;
use App\Entity\Order;
use App\Enum\OrderStatusEnum;
use App\Factory\UserFactory;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use App\Service\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use PHPUnit\Framework\TestCase;

class OrderServiceTest extends TestCase
{
    private $entityManager;
    private $orderRepository;
    private $userRepository;
    private $paginatorInterface;
    private OrderService $orderService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->orderRepository = $this->createMock(OrderRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->paginatorInterface = $this->createMock(PaginatorInterface::class);

        $this->entityManager->expects($this->any())
            ->method('persist')
            ->with($this->isInstanceOf(Order::class));
        $this->entityManager->expects($this->any())
            ->method('flush');

        $this->orderService = new OrderService(
            $this->entityManager,
            $this->orderRepository,
            $this->userRepository,
            $this->paginatorInterface
        );
    }

    public function testCreateOrderSuccessfully(): void
    {
        $data = new CreateOrderRequest();
        $data->userId = 1;

        $user = UserFactory::create(['email' => 'user@example.com']);

        $this->userRepository->expects($this->once())
            ->method('find')
            ->with($data->userId)
            ->willReturn($user);

        $item = new OrderItemDTO();
        $item->productName = 'Keyboard';
        $item->unitPrice = 45.50;
        $item->quantity = 1;
        $data->items = [$item];

        $order = $this->orderService->createOrder($data->userId, $data->items);
        $this->assertSame('user@example.com', $order->getUser()->getEmail());
        $this->assertSame(OrderStatusEnum::NEW, $order->getStatus());
        $this->assertEquals(45.50, $order->getTotalPrice());
        $this->assertCount(1, $order->getItems());
    }
}