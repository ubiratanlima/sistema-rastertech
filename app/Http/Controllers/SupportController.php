<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupportController extends Controller
{
    /**
     * Lista clientes ativos que possuem veículos vinculados (Nível Atendimento).
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Query principal para buscar clientes com veículos
        $query = DB::table('customers')
            ->join('vehicles', 'customers.id', '=', 'vehicles.customer_id')
            ->leftJoin('devices', 'vehicles.id', '=', 'devices.vehicle_id')
            ->select(
                'customers.id',
                'customers.name',
                'customers.document',
                'customers.code as customer_rtech',
                DB::raw("COUNT(CASE WHEN devices.status = 'active' THEN 1 END) as vehicle_count")
            )
            ->groupBy('customers.id', 'customers.name', 'customers.document', 'customers.code');

        // Filtro tático rápido (Nome, CNPJ/CPF ou RTECH CODE do equipamento vinculado)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('customers.name', 'like', "%{$search}%")
                  ->orWhere('customers.document', 'like', "%{$search}%")
                  ->orWhereExists(function ($sub) use ($search) {
                      $sub->select(DB::raw(1))
                          ->from('devices')
                          ->join('vehicles as v_sub', 'devices.id', '=', 'v_sub.id')
                          ->whereColumn('v_sub.customer_id', 'customers.id')
                          ->where('devices.model_description', 'like', "%{$search}%");
                  });
            });
        }

        $customers = $query->paginate(15);

        // Injeção de dados de veículos para o sistema de Acordeon
        foreach ($customers as $customer) {
            $customer->vehicles = DB::table('vehicles')
                ->leftJoin('devices', 'vehicles.id', '=', 'devices.vehicle_id')
                ->leftJoin('gsm_cards', 'devices.gsm_card_id', '=', 'gsm_cards.id')
                ->where('vehicles.customer_id', $customer->id)
                ->select(
                    'vehicles.*',
                    'devices.model_description as rtech_code',
                    'devices.imei',
                    'devices.status as device_status',
                    'gsm_cards.phone_number',
                    'gsm_cards.operator'
                )
                ->get();
            
            // Pega o código RTECH de um dos veículos para a linha de base (se houver)
            $customer->primary_rtech = $customer->vehicles->first()->rtech_code ?? '---';
        }

        return view('support.index', compact('customers', 'search'));
    }
}
