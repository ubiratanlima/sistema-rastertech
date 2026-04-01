<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company(),
            'company_name' => $this->faker->companySuffix(),
            'email' => $this->faker->unique()->companyEmail(),
            'document' => $this->faker->numerify('##############'), 
            'code' => $this->faker->numerify('#####'), 
            'cell_phone' => $this->faker->numerify('129########'),
            'landline_phone' => $this->faker->numerify('123#######'),
            'zip_code' => $this->faker->numerify('12#####-###'), // Just placeholder structure
            'street' => $this->faker->streetName(),
            'number' => $this->faker->buildingNumber(),
            'neighborhood' => $this->faker->citySuffix(),
            'city' => $this->faker->city(),
        ];
    }
}
