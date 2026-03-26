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

    /**
     * Inativa um Equipamento com trava de segurança.
     */
    public function destroy($id)
    {
        // 🔒 VERIFICAÇÃO DE SEGURANÇA: O rastreador está instalado em algum carro?
        $device = DB::table('devices')->where('id', $id)->first();

        if ($device && $device->vehicle_id) {
            return redirect()
                ->route('devices.index')
                ->with('error', 'Este rastreador está instalado em um veículo. Desinstale o equipamento antes de remover.');
        }

        // 🛡️ SEGURO PARA OPERAÇÃO
        try {
            DB::table('devices')->where('id', $id)->delete();
            return redirect()
                ->route('devices.index')
                ->with('success', 'Hardware removido do inventário com sucesso!');
        } catch (\Exception $e) {
            return redirect()
                ->route('devices.index')
                ->with('error', 'Ocorreu um erro técnico ao tentar remover o equipamento.');
        }
    }
}
