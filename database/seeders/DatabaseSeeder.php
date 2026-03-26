<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\GsmCard;
use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\Platform;
use App\Models\Provider;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. USUÁRIO MESTRE
        User::updateOrCreate(['email' => 'admin@rastertech.com'], [
            'name' => 'Ubiratan Admin',
            'password' => bcrypt('m45t3rMASTER'),
        ]);

        // 2. CLIENTE PADRÃO (ESTOQUE GERAL)
        $stock = Customer::firstOrCreate(['name' => 'EMBRAET ESTOQUE GERAL'], ['is_default_stock' => true]);

        // 3. INFRAESTRUTURA
        $this->call(DeviceModelSeeder::class);
        $providers = Provider::factory(10)->create();
        $platforms = Platform::factory(5)->create();
        $customers = Customer::factory(50)->create();
        $deviceModels = DeviceModel::all();

        // 4. VEÍCULOS
        foreach ($customers as $c) {
            Vehicle::factory(4)->create(['customer_id' => $c->id]);
        }
        $vehicles = Vehicle::all();

        // 5. CHIPS (500 CHIPS NO TOTAL)
        $cards = GsmCard::factory(500)->create([
            'provider_id' => $providers->random()->id
        ]);

        // 6. RASTREADORES (1.000 DEVICES) - LÓGICA DE ESTOQUE FLEXÍVEL
        for ($i = 0; $i < 1000; $i++) {
            // Apenas os primeiros 300 estarão VINCULADOS (Em uso)
            $isInstalled = $i < 300; 
            
            Device::create([
                'imei' => '864' . fake()->unique()->numerify('############'),
                'model_description' => 'Equipamento Rev. ' . ($i % 5),
                'device_model_id' => $deviceModels->random()->id,
                
                // Só vincula se estiver instalado
                'platform_id' => $isInstalled ? $platforms->random()->id : null,
                'port_number' => $isInstalled ? fake()->numerify('####') : null,
                'gsm_card_id' => $isInstalled ? $cards->get($i)->id : null, // (0-299)
                'customer_id' => $isInstalled ? $customers->random()->id : $stock->id,
                'vehicle_id' => $isInstalled ? $vehicles->random()->id : null,
                
                'provider_id' => $providers->random()->id,
                'status' => $isInstalled ? 'active' : 'inactive'
            ]);
        }

        // 200 Chips sobraram no estoque sem rastreador (200-499 na coleção $cards)
        User::factory(200)->create();
    }
}
