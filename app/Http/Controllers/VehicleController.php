<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleController extends Controller
{
    /**
     * Lista a Frota unificada (Veículo + Cliente + Rastreador + Chip).
     */
    public function index()
    {
        $vehicles = DB::table('vehicles')
            ->join('customers', 'vehicles.customer_id', '=', 'customers.id')
            ->leftJoin('devices', 'vehicles.id', '=', 'devices.vehicle_id')
            ->leftJoin('gsm_cards', 'devices.gsm_card_id', '=', 'gsm_cards.id')
            ->select(
                'vehicles.id',
                'vehicles.plate',
                'vehicles.brand',
                'vehicles.model as vehicle_model',
                'customers.name as customer_name',
                'devices.imei as device_imei',
                'devices.status as device_status',
                'gsm_cards.phone_number as sim_number',
                'gsm_cards.operator as sim_operator'
            )
            ->paginate(15)
            ->withPath('/fleets');

        return view('fleets.index', compact('vehicles'));
    }

    /**
     * Inativa um veículo com trava de segurança (0-Ativos).
     */
    public function destroy($id)
    {
        // 🔒 VERIFICAÇÃO DE SEGURANÇA: O veículo possui tecnologia instalada?
        $hasActiveDevice = DB::table('devices')
            ->where('vehicle_id', $id)
            ->exists();

        if ($hasActiveDevice) {
            return redirect()
                ->route('fleets.index')
                ->with('error', 'O veículo possui um rastreador vinculado. Desvincule o equipamento antes de inativar.');
        }

        // 🛡️ SEGURO PARA OPERAÇÃO
        try {
            DB::table('vehicles')->where('id', $id)->delete();
            return redirect()
                ->route('fleets.index')
                ->with('success', 'Veículo removido da frota com sucesso!');
        } catch (\Exception $e) {
            return redirect()
                ->route('fleets.index')
                ->with('error', 'Ocorreu um erro técnico ao tentar remover o veículo.');
        }
    }
}
