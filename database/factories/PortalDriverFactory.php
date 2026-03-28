<?php

namespace Database\Factories;

use App\Models\PortalDriver;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class PortalDriverFactory extends Factory
{
    protected $model = PortalDriver::class;

    public function definition(): array
    {
        // 🚥 LÓGICA DE DATAS (VARIANDO ESTADOS OPERACIONAIS)
        $statuses = [
            'valid' => now()->addMonths(rand(1, 24)), 
            'warning' => now()->addDays(rand(1, 19)),     
            'expired' => now()->subDays(rand(1, 45)),    
        ];

        $type = $this->faker->randomElement(['valid', 'warning', 'expired']);
        $expiry = $statuses[$type];

        return [
            'customer_id' => 1, // Será sobrescrito pelo Seeder
            'name' => $this->faker->name,
            'father_name' => $this->faker->name('male'),
            'mother_name' => $this->faker->name('female'),
            'birth_date' => $this->faker->date('Y-m-d', '-25 years'),
            'birth_place' => $this->faker->city . ' / ' . $this->faker->stateAbbr,
            'nationality' => 'Brasileira',
            'cpf' => $this->faker->unique()->numerify('###.###.###-##'),
            'rg' => $this->faker->unique()->numerify('##.###.###-#'),
            'issuer' => 'SSP',
            'uf' => $this->faker->stateAbbr,
            'cnh_number' => $this->faker->unique()->numerify('###########'), 
            'issue_date' => now()->subYears(2),
            'cnh_expiry' => $expiry,
            'category' => $this->faker->randomElement(['A', 'B', 'AB', 'D', 'AD', 'AE']),
            'territory_validity' => 'NACIONAL',
            'status' => ($type === 'expired' ? 'blocked' : 'active'),
            'cnh_front_path' => null, 
            'cnh_back_path' => null,
            'last_checklist_at' => null,
        ];
    }
}
