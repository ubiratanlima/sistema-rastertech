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

        // Simulando dados de telemetria
        $onlineNow = round($totalDevices * 0.73);
        $criticalAlerts = 12;

        return view('dashboard', compact(
            'totalDevices', 
            'totalSims', 
            'onlineNow', 
            'criticalAlerts',
            'chartLabels',
            'chartData'
        ));
    }
}
