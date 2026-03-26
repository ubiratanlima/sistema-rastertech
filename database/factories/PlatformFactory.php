<?php

namespace Database\Factories;

use App\Models\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlatformFactory extends Factory
{
    protected $model = Platform::class;

    public function definition(): array
    {
        return [
            'name' => 'Plataforma ' . $this->faker->word(),
            'url' => $this->faker->url(),
            'server_ip' => $this->faker->ipv4(),
            'supplier_name' => $this->faker->name(),
        ];
    }
}
