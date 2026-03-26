<?php

namespace Database\Factories;

use App\Models\Vehicle;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        return [
            'plate' => strtoupper($this->faker->bothify('???-#?##')),
            'brand' => $this->faker->randomElement(['Fiat', 'VW', 'Ford', 'Toyota']),
            'model' => $this->faker->word(),
            'customer_id' => Customer::factory(),
        ];
    }
}
