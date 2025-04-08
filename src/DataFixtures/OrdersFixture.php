<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use App\Enum\OrderStatusEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class OrdersFixture extends Fixture implements DependentFixtureInterface
{
    use FakerTrait;

    /**
     * @return array<array-key, class-string<Fixture>>
     */
    public function getDependencies(): array
    {
        return [UserFixture::class];
    }

    public function load(ObjectManager $manager): void
    {
        /** @var array<array-key, User> $users */
        $users = $manager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            for ($i = 1; $i <= 5; ++$i) {
                $order = (new Order())
                    ->setUser($user)
                    ->setStatus($this->faker()->randomElement(OrderStatusEnum::cases()))
                    ->setTotalPrice($this->faker()->randomFloat(2, 0, 100))
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->addItem(
                        (new OrderItem())
                            ->setProductName($this->faker()->word())
                            ->setUnitPrice($this->faker()->randomFloat(2, 0, 100))
                            ->setQuantity($this->faker()->numberBetween(1, 10))
                    );

                $manager->persist($order);
            }
        }

        $manager->flush();
    }
}