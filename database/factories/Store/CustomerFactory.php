<?php

namespace Database\Factories\Store;

use App\Models\Store\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'lastname' => $this->faker->lastName,
            'firstname' => $this->faker->firstName,
            'store_id' => $this->faker->randomDigitNotNull,
            'email' => $this->faker->safeEmail,
            'promotionals' => $this->faker->randomDigitNotNull,
            'address' => $this->faker->streetAddress,
            'apartment' => $this->faker->randomKey,
            'city' => $this->faker->county,
            'state' => $this->faker->region,
            'country' => $this->faker->country,
            'postal' => $this->faker->postcode
        ];
    }
}
