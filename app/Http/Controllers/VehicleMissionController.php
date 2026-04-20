<?php

namespace App\Http\Controllers;

use App\Models\VehicleMission;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\PortalDriver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleMissionController extends Controller
{
    /**
     * Exibe a Torre de Controle de Missões Administrativa.
     */
    public function index(Request $request)
    {
        // Filtro padrão: Apenas jornadas em andamento (open) no primeiro carregamento
        $status = $request->has('status') ? $request->input('status') : 'open';
        $customerId = $request->input('customer_id');
        $vehicleId = $request->input('vehicle_id');
        $driverId = $request->input('driver_id');
        $search = $request->input('search');

        $query = VehicleMission::with(['customer', 'vehicle', 'driver', 'entryChecklist', 'exitChecklist']);

        // Filtro por Status
        if ($status) {
            $query->where('status', $status);
        }

        // Filtro por Cliente
        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        // Filtro por Veículo
        if ($vehicleId) {
            $query->where('vehicle_id', $vehicleId);
        }

        // Filtro por Motorista
        if ($driverId) {
            $query->where('driver_id', $driverId);
        }

        // Pesquisa Geral (Placa, Motorista ou Cliente)
        if ($search) {
            $searchLower = strtolower($search);
            $query->where(function($q) use ($searchLower) {
                $q->whereHas('vehicle', function($vq) use ($searchLower) {
                    $vq->where(DB::raw('LOWER(plate)'), 'like', "%{$searchLower}%");
                })
                ->orWhereHas('driver', function($dq) use ($searchLower) {
                    $dq->where(DB::raw('LOWER(name)'), 'like', "%{$searchLower}%");
                })
                ->orWhereHas('customer', function($cq) use ($searchLower) {
                    $cq->where(DB::raw('LOWER(name)'), 'like', "%{$searchLower}%")
                      ->orWhere(DB::raw('LOWER(company_name)'), 'like', "%{$searchLower}%");
                });
            });
        }

        $missions = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Dados para os Filtros no Blade
        $customers = Customer::orderBy('name')->get(['id', 'name']);
        $vehicles = Vehicle::orderBy('plate')->get(['id', 'plate']);
        $drivers = PortalDriver::orderBy('name')->get(['id', 'name']);

        return view('missions.index', compact('missions', 'customers', 'vehicles', 'drivers', 'status', 'customerId', 'vehicleId', 'driverId', 'search'));
    }
}
