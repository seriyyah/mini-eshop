<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateOrderRequest
{
    #[Assert\NotBlank]
    public int $userId;

    /**
     * @var OrderItemDTO[]
     */
    #[Assert\NotBlank]
    #[Assert\All(constraints: [new Assert\Type(OrderItemDTO::class)])]
    #[Assert\Valid]
    public array $items;
}