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

class DriverTestSeeder extends Seeder
{
    public function run(): void
    {
        // 1. GARANTIR UM CLIENTE PARA O TESTE
        $customer = Customer::firstOrCreate(
            ['name' => 'RASTERTECH LOGISTICA'],
            [
                'company_name' => 'RASTERTECH TRANSPORTES LTDA',
                'email' => 'logistica@rastertech.com.br',
                'document' => '00.000.000/0001-99',
                'code' => 'RTECH-001'
            ]
        );

        // 2. GARANTIR UM VEÍCULO PARA O CHECK-IN
        Vehicle::firstOrCreate(
            ['plate' => 'RTH-2026'],
            [
                'customer_id' => $customer->id,
                'brand' => 'SCANIA',
                'model' => 'R450 - HIGH LINE',
                'color' => 'BRANCO',
                'year' => 2024
            ]
        );

        // 3. GARANTIR UMA PLATAFORMA (OBRIGATÓRIO PARA SUB_USER)
        $platform = Platform::firstOrCreate(
            ['name' => 'RASTERTECH HUB'],
            [
                'url' => 'https://hub.rastertech.com.br',
                'server_ip' => '127.0.0.1',
                'supplier_name' => 'RASTERTECH INTERNAL'
            ]
        );

        // 4. CRIAR O "SUB_USER" (PONTE TÁTICA)
        $subUser = CustomerSubUser::updateOrCreate(
            ['email' => 'motorista@rastertech.com.br'],
            [
                'customer_id' => $customer->id,
                'platform_id' => $platform->id,
                'name' => 'Jeferson Motorista (Especialista)',
                'external_username' => 'jeff_rtech',
                'external_password' => 'raster123',
                'role' => 'operator',
                'access_validated' => true,
                'email_verified_at' => now()
            ]
        );

        // 5. CRIAR O USER (O QUE FAZ O LOGIN)
        User::updateOrCreate(
            ['email' => 'motorista@rastertech.com.br'],
            [
                'name' => 'Jeferson Especialista',
                'password' => Hash::make('raster123'),
                'role' => 'operator',
                'customer_id' => $customer->id,
                'external_username' => $subUser->external_username,
                'external_password' => $subUser->external_password,
                'gender' => 'Masculino',
                'access_validated' => true,
                'email_verified_at' => now()
            ]
        );

        // 6. CRIAR O PERFIL DE MOTORISTA (O QUE FAZ A JORNADA)
        PortalDriver::updateOrCreate(
            ['sub_user_id' => $subUser->id],
            [
                'customer_id' => $customer->id,
                'name' => 'Jeferson Especialista RTECH',
                'cnh_number' => '12345678900',
                'cnh_expiry' => '2030-12-31',
                'status' => 'active'
            ]
        );
    }
}
