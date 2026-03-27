<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DevicePositionSeeder extends Seeder
{
    public function run(): void
    {
        $devices = DB::table('devices')->pluck('id');

        if ($devices->isEmpty()) return;

        $positions = [
            ['lat' => -23.5505, 'lng' => -46.6333, 'city' => 'São Paulo'],
            ['lat' => -12.9704, 'lng' => -38.5023, 'city' => 'Salvador'],
            ['lat' => -25.4290, 'lng' => -49.2671, 'city' => 'Curitiba'],
            ['lat' => -3.7319, 'lng' => -38.5267, 'city' => 'Fortaleza'],
            ['lat' => -15.7938, 'lng' => -47.8827, 'city' => 'Brasília'],
        ];

        foreach ($devices as $index => $deviceId) {
            $basePos = $positions[$index % count($positions)];
            
            DB::table('device_positions')->insert([
                'device_id' => $deviceId,
                'latitude' => $basePos['lat'] + (rand(-100, 100) / 1000),
                'longitude' => $basePos['lng'] + (rand(-100, 100) / 1000),
                'speed' => rand(0, 110),
                'ignition' => (bool)rand(0, 1),
                'transmitted_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
