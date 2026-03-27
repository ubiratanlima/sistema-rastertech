<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Exibe o Dashboard com dados dinâmicos do Banco.
     */
    public function index()
    {
        // 🧬 Contadores Totais
        $totalDevices = DB::table('devices')->count() ?? 1000;
        $totalSims = DB::table('gsm_cards')->count() ?? 500;
        
        // 📊 Consulta para o Gráfico Donut (Distribuição por Modelo)
        $modelDistribution = DB::table('devices')
            ->leftJoin('device_models', 'devices.device_model_id', '=', 'device_models.id')
            ->select('device_models.name as model_name', DB::raw('count(*) as total'))
            ->groupBy('device_models.name')
            ->get();

        // Preparando dados para o Chart.js
        $chartLabels = $modelDistribution->pluck('model_name')->toArray();
        $chartData = $modelDistribution->pluck('total')->toArray();

        // 🛰️ Telemetria Real (Última Posição de cada Dispositivo)
        $latestPositions = DB::table('device_positions')
            ->join('devices', 'device_positions.device_id', '=', 'devices.id')
            ->select('device_positions.latitude', 'device_positions.longitude', 'devices.imei')
            ->latest('device_positions.created_at')
            ->take(50)
            ->get();

        $onlineNow = DB::table('devices')->where('status', 'active')->count();
        $criticalAlerts = DB::table('gsm_cards')->where('status', 'suspended')->count();

        return view('dashboard', compact(
            'totalDevices', 
            'totalSims', 
            'onlineNow', 
            'criticalAlerts',
            'chartLabels',
            'chartData',
            'latestPositions'
        ));
    }
}
