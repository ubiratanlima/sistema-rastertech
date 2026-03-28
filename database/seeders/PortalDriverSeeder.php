<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\PortalDriver;

class PortalDriverSeeder extends Seeder
{
    public function run(): void
    {
        // 🚥 LÓGICA DE DETECÇÃO TÁTICA
        $customerId = DB::table('customers')->value('id');

        if (!$customerId) {
            // Se não houver cliente, criamos um mestre para o teste
            $customerId = DB::table('customers')->insertGetId([
                'name' => 'CLIENTE MASTER RASTERTECH',
                'email' => 'master@rastertech.com',
                'cnpj' => '00.000.000/0001-91',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 🏗️ INJEÇÃO MASSA (30 MOTORISTAS POR CLIENTE)
        $customers = DB::table('customers')->get();

        foreach ($customers as $customer) {
            echo "Injetando motoristas para cliente: {$customer->name}\n";
            
            PortalDriver::factory()->count(30)->create([
                'customer_id' => $customer->id
            ]);
        }
        
        echo "✅ CARGA TÁTICA CONCLUÍDA!\n";
    }
}
