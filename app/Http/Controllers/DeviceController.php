<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeviceController extends Controller
{
    /**
     * Lista todos os aparelhos no inventário.
     */
    public function index()
    {
        // 🧬 Buscando dispositivos com joins para mostrar Cliente e Modelo
        $devices = DB::table('devices')
            ->leftJoin('customers', 'devices.customer_id', '=', 'customers.id')
            ->leftJoin('device_models', 'devices.device_model_id', '=', 'device_models.id')
            ->leftJoin('gsm_cards', 'devices.gsm_card_id', '=', 'gsm_cards.id')
            ->select(
                'devices.*', 
                'customers.name as customer_name', 
                'device_models.name as model_name',
                'gsm_cards.iccid as sim_iccid'
            )
            ->paginate(15);

        return view('devices.index', compact('devices'));
    }
}
