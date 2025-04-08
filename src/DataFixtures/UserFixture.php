<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    use FakerTrait;

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $user = (new User())
                ->setEmail('user+' . $i . '@example.com')
                ->setPassword('password');

            $manager->persist($user);
        }

        $manager->flush();
    }
}