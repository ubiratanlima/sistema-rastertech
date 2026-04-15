<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Customer;
use App\Models\PortalDriver;
use App\Models\CustomerSubUser;
use App\Models\Vehicle;
use App\Models\Platform;
use Illuminate\Support\Facades\Hash;

class ConcurrencyTestSeeder extends Seeder
{
    public function run(): void
    {
        // 1. GARANTIR O CLIENTE ALVO
        $customer = Customer::firstOrCreate(
            ['company_name' => 'RASTERTECH TRANSPORTES LTDA'],
            [
                'name' => 'RASTERTECH LOGISTICA',
                'email' => 'logistica@rastertech.com.br',
                'document' => '00.000.000/0001-99',
                'code' => 'RTECH-001'
            ]
        );

        // 2. GARANTIR A PLATAFORMA DE CONTEXTO
        $platform = Platform::firstOrCreate(['name' => 'RASTERTECH HUB']);

        // 3. CRIAR 2 NOVOS VEÍCULOS PARA CONCORRÊNCIA
        $extraVehicles = [
            ['plate' => 'RTH-2027', 'brand' => 'VOLVO', 'model' => 'FH 540'],
            ['plate' => 'RTH-2028', 'brand' => 'MERCEDES', 'model' => 'ACTROS'],
        ];

        foreach ($extraVehicles as $vData) {
            Vehicle::firstOrCreate(
                ['plate' => $vData['plate']],
                array_merge($vData, [
                    'customer_id' => $customer->id
                ])
            );
        }

        // 4. CRIAR OS 5 MOTORISTAS DE TESTE (A até E)
        $letters = ['A', 'B', 'C', 'D', 'E'];
        
        foreach ($letters as $l) {
            $email = "motorista{$l}@rastertech.com";
            $name = "Motorista {$l} (Teste Concorrência)";
            $username = "driver_{$l}_rtech";

            // A. SubUser (Interface com Plataforma - Nível Técnico do Banco)
            $subUser = CustomerSubUser::updateOrCreate(
                ['email' => $email],
                [
                    'customer_id' => $customer->id,
                    'platform_id' => $platform->id,
                    'name' => $name,
                    'external_username' => $username,
                    'external_password' => '12345678',
                    'role' => 'driver', // Aceito pela restrição do banco
                    'access_validated' => true,
                    'email_verified_at' => now()
                ]
            );

            // B. User (Login no sistema - Nível Visual da Interface)
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('12345678'),
                    'role' => 'Motorista', // Exibido no Portal
                    'customer_id' => $customer->id,
                    'external_username' => $username,
                    'external_password' => '12345678',
                    'gender' => 'Masculino',
                    'access_validated' => true,
                    'email_verified_at' => now()
                ]
            );

            // C. PortalDriver (Entidade técnica do Checklist)
            PortalDriver::updateOrCreate(
                ['sub_user_id' => $subUser->id],
                [
                    'customer_id' => $customer->id,
                    'name' => "MOTORISTA {$l} RTECH",
                    'cnh_number' => "0000000000{$l}",
                    'cnh_expiry' => '2030-01-01',
                    'status' => 'active'
                ]
            );
        }

        // 5. CRIAR SUPERVISOR DE LOGÍSTICA (PARA TESTE DE VISIBILIDADE GLOBAL)
        User::updateOrCreate(
            ['email' => 'operador@rastertech.com'],
            [
                'name' => 'Supervisor Logística (RASTERTECH)',
                'password' => Hash::make('12345678'),
                'role' => 'operator', // Papel de gestão
                'customer_id' => $customer->id,
                'access_validated' => true,
                'email_verified_at' => now()
            ]
        );
    }
}
