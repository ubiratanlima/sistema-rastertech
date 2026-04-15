<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VehicleChecklist;
use App\Models\VehicleMission;

class SyncVehicleMissions extends Command
{
    protected $signature = 'rastertech:sync-missions';
    protected $description = 'Migra checklists individuais para a nova estrutura de missões agrupadas';

    public function handle()
    {
        $this->info('Iniciando sincronização de missões...');

        // 🟢 1. Pegamos todas as ENTRADAS (Check-ins) que ainda não foram migradas
        $entries = VehicleChecklist::where('type', 'entry')
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($entries as $entry) {
            // Verificamos se já existe uma missão para este ID de entrada
            if (VehicleMission::where('entry_id', $entry->id)->exists()) {
                continue;
            }

            // 🛡️ TRATAMENTO TÁTICO: Se o checklist não tiver customer_id, buscamos no veículo
            $customerId = $entry->customer_id ?? ($entry->vehicle ? $entry->vehicle->customer_id : null);

            if (!$customerId) {
                $this->error("Pulando entrada #{$entry->id}: Cliente não identificado.");
                continue;
            }

            $this->info("Criando missão para entrada #{$entry->id} (Veículo: {$entry->vehicle_id})");

            // Criamos a missão
            $mission = VehicleMission::create([
                'customer_id' => $customerId,
                'vehicle_id' => $entry->vehicle_id,
                'driver_id' => $entry->driver_id,
                'entry_id' => $entry->id,
                'status' => 'open',
                'created_at' => $entry->created_at,
                'updated_at' => $entry->created_at
            ]);

            // 🔵 2. Tentamos encontrar a primeira SAÍDA (Checkout) correspondente
            $exit = VehicleChecklist::where('vehicle_id', $entry->vehicle_id)
                ->where('customer_id', $entry->customer_id)
                ->where('type', 'exit')
                ->where('id', '>', $entry->id)
                ->orderBy('id', 'asc')
                ->first();

            if ($exit) {
                $mission->update([
                    'exit_id' => $exit->id,
                    'status' => 'closed',
                    'updated_at' => $exit->created_at
                ]);
                $this->info(" -> Checkout #{$exit->id} vinculado com sucesso.");
            }
        }

        $this->info('Sincronização concluída!');
    }
}
