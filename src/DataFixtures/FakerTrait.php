<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;

trait FakerTrait
{
    private ?Generator $faker = null;

    public function faker(): Generator
    {
        if (null === $this->faker) {
            $this->faker = Factory::create('en_EN');
        }

        return $this->faker;
    }
}