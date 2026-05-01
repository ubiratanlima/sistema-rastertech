<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$plate = 'CDF0007';
$vehicle = \App\Models\Vehicle::withTrashed()
    ->where('plate', 'like', "%$plate%")
    ->first();

if ($vehicle) {
    echo "\n✅ VEÍCULO ENCONTRADO!\n";
    echo "ID: " . $vehicle->id . "\n";
    echo "Placa: " . $vehicle->plate . "\n";
    echo "Status: " . ($vehicle->deleted_at ? "DELETADO (Lixeira)" : "ATIVO") . "\n";
    echo "ID Cliente: " . $vehicle->customer_id . "\n";
} else {
    echo "\n❌ VEÍCULO NÃO ENCONTRADO NO BANCO DE DADOS.\n";
    $latest = \App\Models\Vehicle::latest()->first();
    if ($latest) {
        echo "O último veículo cadastrado foi: " . $latest->plate . " (ID: " . $latest->id . ")\n";
    }
}
