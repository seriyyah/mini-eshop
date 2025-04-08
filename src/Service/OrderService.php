<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Enum\OrderStatusEnum;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;

class OrderService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly OrderRepository $orderRepository,
        private readonly UserRepository $userRepository
    ) {
    }

    public function createOrder(
        int $userId,
        array $items
    ): Order {
        $user = $this->userRepository->find($userId);

        $order = new Order();
        $order->setUser($user)
            ->setStatus(OrderStatusEnum::NEW)
            ->setCreatedAt(new \DateTimeImmutable());

        $totalPrice = 0.0;

        foreach ($items as $item) {
            $orderItem = new OrderItem();
            $orderItem->setProductName($item->productName)
                ->setUnitPrice($item->unitPrice)
                ->setQuantity($item->quantity);
            $order->addItem($orderItem);
            $totalPrice += $orderItem->getUnitPrice() * $orderItem->getQuantity();
        }

        $order->setTotalPrice($totalPrice);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }

    public function getAllOrders(): array
    {
        return $this->orderRepository->findAll();
    }

    public function fetchOrder(Order $order): array
    {
        return [
            'id' => $order->getId(),
            'customerEmail' => $order->getUser()?->getEmail(),
            'status' => $order->getStatus()->value,
            'totalPrice' => $order->getTotalPrice(),
            'createdAt' => $order->getCreatedAt()?->format(DateTimeInterface::ATOM),
            'items' => array_map(static fn($item) => [
                'productName' => $item->getProductName(),
                'unitPrice' => $item->getUnitPrice(),
                'quantity' => $item->getQuantity()
            ], $order->getItems()->toArray())
        ];
    }

    public function fetchAllOrders(array $orders): array
    {
        return array_map(fn($order) => $this->fetchOrder($order), $orders);
    }
}