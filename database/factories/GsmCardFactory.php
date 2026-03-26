<?php

namespace Database\Factories;

use App\Models\GsmCard;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

class GsmCardFactory extends Factory
{
    protected $model = GsmCard::class;

    public function definition(): array
    {
        return [
            'iccid' => $this->faker->numerify('8955################'),
            'phone_number' => $this->faker->phoneNumber(),
            'operator' => $this->faker->randomElement(['Vivo', 'Claro', 'Tim', 'Arqia', 'Links']),
            'apn' => 'apn.m2m.com.br',
            'apn_user' => 'm2m',
            'apn_pass' => 'm2m',
            'provider_id' => Provider::factory(),
            'status' => 'active',
        ];
    }
}
