<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class OrderItemDTO
{
    #[Assert\NotBlank]
    public string $productName;

    #[Assert\NotBlank]
    #[Assert\Type('float')]
    #[Assert\Positive]
    public float $unitPrice;

    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    #[Assert\Positive]
    public int $quantity;
}