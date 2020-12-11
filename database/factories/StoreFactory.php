<?php

namespace Database\Factories;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

class StoreFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Store::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name("Male") . ' Store',
            'shortname' => $this->faker->userName,
            'industry' => array_rand(['Fashion', 'Electronics', 'Textile']),
            'category' => array_rand(['Children Wear', 'Women Shoes', 'Men Clothing']),
            'size' => '1-20',
            'current_billing_plan' => 'Basic',
            'card_type' => $this->faker->creditCardType,
            'last4' => 2345,
            'bank' => array_rand(['UBA', 'Access', 'Zenith', 'Diamond', 'GTBank']),
            'channel' => 'card',
            'exp_month' => $this->faker->creditCardExpirationDate,
            'exp_year' => $this->faker->creditCardExpirationDate,
            'billing_address' => $this->faker->address,
            'authorization_code' => $this->faker->uuid,
            'trial_ends_at' => 2021
        ];
    }
}
