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
        $user = auth()->user();
        $role = strtolower($user->role ?? '');

        // 🎯 REDIRECIONAMENTO INTELIGENTE (Raiz)
        // Se for Cliente ou Autorizado, o lugar dele é no Portal de Clientes
        if (in_array($role, ['cliente', 'autorizado'])) {
            return redirect()->route('portal.dashboard');
        }

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

        // 👥 CENSO DE USUÁRIOS (TÁTICO, CASE-INSENSITIVE E MAPEADO)
        $countAdmins      = DB::table('users')->where('role', 'Administrador')->whereNull('deleted_at')->count();
        $countGerentes    = DB::table('users')->where('role', 'Gerente')->whereNull('deleted_at')->count();
        $countClientes    = DB::table('users')->where('role', 'Cliente')->whereNull('deleted_at')->count();
        $countSuporte     = DB::table('users')->where('role', 'Suporte')->whereNull('deleted_at')->count();
        $countInstaladores = DB::table('users')->where('role', 'Instalador')->whereNull('deleted_at')->count();
        $countMotoristas  = DB::table('customer_sub_users')->where('role', 'Motorista')->whereNull('deleted_at')->count();
        $countAutorizados = DB::table('customer_sub_users')->where('role', 'Autorizado')->whereNull('deleted_at')->count();

        return view('dashboard', compact(
            'totalDevices', 
            'totalSims', 
            'onlineNow', 
            'criticalAlerts',
            'chartLabels',
            'chartData',
            'latestPositions',
            'countAdmins',
            'countGerentes',
            'countClientes',
            'countSuporte',
            'countInstaladores',
            'countMotoristas',
            'countAutorizados'
        ));
    }
}
