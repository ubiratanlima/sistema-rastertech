<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleController extends Controller
{
    /**
     * Lista a Frota unificada (Veículo + Cliente + Rastreador + Chip).
     */
    /**
     * Lista a Frota unificada (Veículo + Cliente + Rastreador + Chip).
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $view = $request->input('view', 'active');
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');

        $query = \App\Models\Vehicle::with(['customer', 'devices.gsmCard.provider', 'devices.deviceModel', 'devices.platform'])
            ->withCount('devices');

        // 👁️ FILTRO DE VISÃO (REGRA: Ativo = Com Rastreador)
        // 🔍 LÓGICA DE VISÃO RASTERTECH
        if ($search) {
            // Se houver busca, procuramos em TUDO (Global Search)
            $query->withTrashed();
            
            $searchLower = strtolower($search);
            $searchClean = str_replace('-', '', $searchLower);
            
            $query->where(function($q) use ($searchLower, $searchClean) {
                $q->where(DB::raw("REPLACE(LOWER(plate), '-', '')"), 'like', "%{$searchClean}%")
                  ->orWhere(DB::raw('LOWER(brand)'), 'like', "%{$searchLower}%")
                  ->orWhere(DB::raw('LOWER(model)'), 'like', "%{$searchLower}%")
                  ->orWhereHas('customer', function($cq) use ($searchLower) {
                      $cq->where(DB::raw('LOWER(name)'), 'like', "%{$searchLower}%")
                        ->orWhere(DB::raw('LOWER(company_name)'), 'like', "%{$searchLower}%");
                  });
            });
        } else {
            // Filtros normais por aba
            if ($view === 'trash') {
                $query->onlyTrashed();
            } elseif ($view === 'inactive') {
                $query->whereDoesntHave('devices');
            } else {
                $query->whereHas('devices');
            }
        }

        $vehicles = $query->orderBy($sort, $direction)
            ->paginate(15)
            ->withPath('/fleets')
            ->withQueryString();

        $freeDevices = \App\Models\Device::whereNull('vehicle_id')->orderBy('internal_code')->get(['id', 'internal_code', 'imei']);
        $freeSims = \App\Models\GsmCard::whereDoesntHave('device')->orderBy('iccid')->get(['id', 'iccid', 'phone_number']);
        $customers = \App\Models\Customer::orderBy('name')->get(['id', 'name', 'company_name']);

        return view('fleets.index', compact('vehicles', 'search', 'view', 'sort', 'direction', 'freeDevices', 'freeSims', 'customers'));
    }

    /**
     * Registro de novo veículo com Dossiê Tático
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate' => 'required|string|max:10|unique:vehicles,plate',
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'year' => 'nullable|string|max:4',
            'color' => 'nullable|string|max:30',
            'renavam' => 'nullable|string|max:20',
            'chassi' => 'nullable|string|max:30',
            'customer_id' => 'required|exists:customers,id',
            'photo_front' => 'nullable|image|max:2048',
            'photo_back' => 'nullable|image|max:2048',
        ]);

        // 🔠 PADRONIZAÇÃO RASTERTECH: Placa sempre limpa e em caixa alta
        $validated['plate'] = strtoupper(trim($validated['plate']));

        if ($request->hasFile('photo_front')) {
            $validated['photo_front'] = $request->file('photo_front')->store('vehicles', 'public');
        }
        if ($request->hasFile('photo_back')) {
            $validated['photo_back'] = $request->file('photo_back')->store('vehicles', 'public');
        }

        \App\Models\Vehicle::create($validated);

        // 🧭 REDIRECIONAMENTO INTELIGENTE: Leva para a aba de Inativos para ver o veículo criado
        return redirect()->route('fleets.index', ['view' => 'inactive'])
            ->with('success', "Veículo [{$validated['plate']}] integrado à frota com sucesso! Ele está disponível na aba de inativos para vinculação de hardware.");
    }

    /**
     * Inativa um veículo (Soft Delete)
     */
    public function destroy($id)
    {
        try {
            $vehicle = \App\Models\Vehicle::findOrFail($id);
            
            // 🔒 SEGURANÇA: Se tiver rastreador, não pode inativar sem desvincular
            if ($vehicle->devices()->count() > 0) {
                return redirect()->back()->with('error', 'O veículo possui um rastreador vinculado. Desvincule o equipamento antes de inativar.');
            }

            $vehicle->delete();
            return redirect()->route('fleets.index')->with('success', 'Veículo inativado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao tentar inativar o veículo.');
        }
    }

    /**
     * Restaura um veículo da lixeira
     */
    public function restore($id)
    {
        $vehicle = \App\Models\Vehicle::withTrashed()->findOrFail($id);
        $vehicle->restore();

        return redirect()->route('fleets.index', ['view' => 'active'])
            ->with('success', "Veículo [{$vehicle->plate}] restaurado com sucesso!");
    }
}
