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

        // 🎯 GERAÇÃO DE ECOSSISTEMA DO CLIENTE (PORTAL)
        foreach ($customers as $index => $c) {
            // 👤 Usuário Gestor (Login do Portal)
            User::create([
                'name' => "Gestor {$c->name}",
                'email' => "cliente{$index}@portal.com",
                'role' => 'customer',
                'customer_id' => $c->id,
                'external_username' => 'rtech_' . strtolower(str_replace([' ', '.'], '_', $c->name)) . '_' . $c->id,
                'external_password' => 'secret_' . ($c->code ?? '1234'),
                'password' => bcrypt('cliente123'),
            ]);

            // 🚛 Motoristas Vinculados
            \App\Models\PortalDriver::factory(5)->create(['customer_id' => $c->id]);

            // 📱 Números de WhatsApp Autorizados
            for ($j = 1; $j <= 3; $j++) {
                \App\Models\CustomerWhatsappNumber::create([
                    'customer_id' => $c->id,
                    'whatsapp_number' => '129' . fake()->numerify('########'),
                    'contact_name' => fake()->name(),
                    'label' => fake()->randomElement(['LOGÍSTICA', 'COMERCIAL', 'FINANCEIRO', 'SUPORTE'])
                ]);
            }
        }

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
                'internal_code' => 'RTECH-' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                'imei' => '864' . fake()->unique()->numerify('############'),
                'model_description' => 'Equipamento Rev. ' . ($i % 5),
                'device_model_id' => $deviceModels->random()->id,
                
                // Só vincula se estiver instalado
                'platform_id' => $isInstalled ? $platforms->random()->id : null,
                'port_number' => $isInstalled ? fake()->numerify('####') : null,
                'gsm_card_id' => $isInstalled ? $cards->get($i)->id : null, 
                'customer_id' => $isInstalled ? $customers->random()->id : $stock->id,
                'vehicle_id' => $isInstalled ? $vehicles->random()->id : null,
                
                'provider_id' => $providers->random()->id,
                'status' => $isInstalled ? 'active' : 'inactive'
            ]);
        }

        User::factory(5)->create();
    }
}
