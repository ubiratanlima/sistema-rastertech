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

        $type = request('type', 'inventory');
        $data = collect();

        if ($type === 'chips') {
            $query = GsmCard::with(['device.customer', 'device.vehicle']);
            if (request('operator')) $query->where('operator', request('operator'));
            if (request('status')) $query->where('status', request('status'));
            if (request('ddd')) $query->where('phone_number', 'LIKE', '%' . request('ddd') . '%');
            if (request('linked') === 'no') $query->whereDoesntHave('device');
            if (request('linked') === 'yes') $query->whereHas('device');
            $data = $query->get();
        } elseif ($type === 'vehicles') {
            $data = Vehicle::with('customer')
                           ->when(request('customer_id'), fn($q) => $q->where('customer_id', request('customer_id')))
                           ->get();
        } elseif ($type === 'customers') {
            $data = Customer::when(request('search'), fn($q) => $q->where('name', 'LIKE', '%'.request('search').'%'))->get();
        } elseif ($type === 'users') {
            $data = \App\Models\User::all();
        } elseif ($type === 'sub_users') {
            $data = \App\Models\CustomerSubUser::with('customer')->get();
        }

        if (request('export') === 'pdf') {
            return view('reports.pdf', compact('stats', 'type', 'data'));
        }

        return view('reports.index', compact('stats', 'type', 'data'));
    }
}
