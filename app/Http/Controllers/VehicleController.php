<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleController extends Controller
{
    /**
     * Lista a Frota unificada (Veículo + Cliente + Rastreador + Chip).
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');

        $query = \App\Models\Vehicle::with(['customer', 'devices.gsmCard.provider', 'devices.deviceModel', 'devices.platform']);

        if ($search) {
            $searchLower = strtolower($search);
            $query->where(function($q) use ($searchLower) {
                $q->where(DB::raw('LOWER(plate)'), 'like', "%{$searchLower}%")
                  ->orWhere(DB::raw('LOWER(brand)'), 'like', "%{$searchLower}%")
                  ->orWhere(DB::raw('LOWER(model)'), 'like', "%{$searchLower}%")
                  ->orWhereHas('customer', function($cq) use ($searchLower) {
                      $cq->where(DB::raw('LOWER(name)'), 'like', "%{$searchLower}%")
                        ->orWhere(DB::raw('LOWER(company_name)'), 'like', "%{$searchLower}%");
                  });
            });
        }

        $vehicles = $query->orderBy($sort, $direction)
            ->paginate(15)
            ->withPath('/fleets')
            ->withQueryString();

        $freeDevices = \App\Models\Device::whereNull('vehicle_id')->orderBy('internal_code')->get(['id', 'internal_code', 'imei']);
        $freeSims = \App\Models\GsmCard::whereDoesntHave('device')->orderBy('iccid')->get(['id', 'iccid', 'phone_number']);

        return view('fleets.index', compact('vehicles', 'search', 'sort', 'direction', 'freeDevices', 'freeSims'));
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
