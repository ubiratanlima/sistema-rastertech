<?php

namespace Database\Factories;

use App\Models\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlatformFactory extends Factory
{
    protected $model = Platform::class;

    public function definition(): array
    {
        $platforms = ['Traccar', 'Wialon', 'Gurtam', 'RasterTech v3', 'Softrack'];
        $name = $this->faker->randomElement($platforms) . ' ' . $this->faker->numberBetween(1, 100);
        return [
            'name' => $name,
            'url' => 'https://' . strtolower(str_replace(' ', '', $name)) . '.rastertech.com',
            'server_ip' => $this->faker->ipv4(),
            'supplier_name' => $this->faker->randomElement(['AWS Brasil', 'Google Cloud', 'Azure', 'DigitalOcean']),
            'app_android_url' => 'https://play.google.com/store/apps/details?id=br.com.rastertech.monitoring.' . strtolower(str_replace(' ', '', $name)),
            'app_ios_url' => 'https://apps.apple.com/br/app/rastertech-' . strtolower(str_replace(' ', '', $name)) . '/id' . $this->faker->numerify('#########'),
        ];
    }
}
