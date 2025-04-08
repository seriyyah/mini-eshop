<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\User;

class UserFactory
{
    public static function create(array $attributes = []): User
    {
        $user = new User();
        $user->setEmail($attributes['email'] ?? 'test@example.com');

        if (isset($attributes['password'])) {
            $user->setPassword($attributes['password']);
        } else {
            $user->setPassword('dummy_password');
        }

        return $user;
    }
}