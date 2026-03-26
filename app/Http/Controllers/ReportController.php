<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GsmCard;
use App\Models\Device;
use App\Models\Vehicle;
use App\Models\Customer;

class ReportController extends Controller
{
    /**
     * Painel Geral de Auditoria e Inteligência de Frota.
     */
    public function index()
    {
        // 🧬 Inteligência de Inventário
        $stats = [
            'sims_total' => GsmCard::count(),
            'devices_total' => Device::count(),
            'vehicles_total' => Vehicle::count(),
            'customers_total' => Customer::count(),
            
            // 🚥 Saúde da Frota
            'sims_active' => GsmCard::where('status', 'active')->count(),
            'devices_active' => Device::where('status', 'active')->count(),
        ];

        return view('reports.index', compact('stats'));
    }
}
