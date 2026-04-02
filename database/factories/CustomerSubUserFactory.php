<?php

namespace Database\Factories;

use App\Models\CustomerSubUser;
use App\Models\Customer;
use App\Models\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerSubUserFactory extends Factory
{
    protected $model = CustomerSubUser::class;

    public function definition(): array
    {
        $name = $this->faker->name();
        return [
            'customer_id' => Customer::factory(),
            'platform_id' => Platform::factory(),
            'name' => $name,
            'email' => strtolower(str_replace(' ', '.', $name)) . '@' . $this->faker->freeEmailDomain(),
            'external_username' => 'app_' . strtolower(str_replace(' ', '_', $name)) . '_' . $this->faker->numberBetween(10, 99),
            'external_password' => 'pass' . $this->faker->numerify('####'),
            'role' => $this->faker->randomElement(['driver', 'operator']),
        ];
    }
}
