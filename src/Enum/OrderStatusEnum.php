<?php

namespace App\Enum;

enum OrderStatusEnum: string
{
    case NEW = 'NEW';
    case PROCESSING = 'PROCESSING';
    case COMPLETED = 'COMPLETED';
    case CANCELLED = 'CANCELLED';
}