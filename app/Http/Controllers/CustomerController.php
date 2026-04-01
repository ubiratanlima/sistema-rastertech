<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::withCount(['devices', 'vehicles', 'gsmCards', 'subUsers'])
            ->with(['vehicles.devices.deviceModel', 'vehicles.devices.gsmCard', 'vehicles.devices.platform', 'subUsers'])
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withPath('/customers');

        return view('customers.index', compact('customers'));
    }

    public function destroy(Customer $customer)
    {
        // REGRA DE OURO RASTERTECH: Proibido inativar se houver qualquer vínculo
        $hasVehicles = $customer->vehicles()->count() > 0;
        $hasDevices = $customer->devices()->count() > 0;
        $hasChips = $customer->gsmCards()->count() > 0;
        $hasSubUsers = $customer->subUsers()->count() > 0;

        if ($hasVehicles || $hasDevices || $hasChips || $hasSubUsers) {
            $msg = "BLOQUEIO DE SEGURANÇA: Este cliente possui ";
            $parts = [];
            if ($hasVehicles) $parts[] = $customer->vehicles()->count() . " veículo(s)";
            if ($hasDevices) $parts[] = $customer->devices()->count() . " unidade(s) rastreada(s)";
            if ($hasChips) $parts[] = $customer->gsmCards()->count() . " chip(s)";
            
            return redirect()->back()->with('error', $msg . implode(", ", $parts) . " vinculado(s). Limpe a custódia para inativar.");
        }

        $customer->delete();
        return redirect()->route('customers.index')->with('success', "Cliente inativado com sucesso.");
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:100',
            'company_name' => 'nullable|string|max:200',
            'email' => 'nullable|email|max:100',
            'document' => 'nullable|string|max:20',
            'code' => 'nullable|string|max:50',
            'cell_phone' => 'nullable|string|max:25',
            'landline_phone' => 'nullable|string|max:25',
            'zip_code' => 'nullable|string|max:15',
            'street' => 'nullable|string|max:200',
            'number' => 'nullable|string|max:20',
            'complement' => 'nullable|string|max:100',
            'neighborhood' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'notes' => 'nullable|string'
        ];

        $validated = $request->validate($rules);
        Customer::create($validated);
        return redirect()->route('customers.index')->with('success', "Cliente registrado com sucesso.");
    }

    public function update(Request $request, Customer $customer)
    {
        $rules = [
            'name' => 'required|string|max:100',
            'company_name' => 'nullable|string|max:200',
            'email' => 'nullable|email|max:100',
            'document' => 'nullable|string|max:20',
            'code' => 'nullable|string|max:50',
            'cell_phone' => 'nullable|string|max:25',
            'landline_phone' => 'nullable|string|max:25',
            'zip_code' => 'nullable|string|max:15',
            'street' => 'nullable|string|max:200',
            'number' => 'nullable|string|max:20',
            'complement' => 'nullable|string|max:100',
            'neighborhood' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'notes' => 'nullable|string'
        ];

        $validated = $request->validate($rules);
        $customer->update($validated);
        return redirect()->route('customers.index')->with('success', "Dados do cliente atualizados com sucesso.");
    }
}
