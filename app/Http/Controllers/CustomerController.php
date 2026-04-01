<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Customer;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $view = $request->input('view', 'active');
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');

        $query = Customer::withCount(['devices', 'vehicles', 'gsmCards', 'subUsers'])
            ->with(['vehicles.devices.deviceModel', 'vehicles.devices.gsmCard', 'vehicles.devices.platform', 'subUsers']);

        // 👁️ FILTRO DE VISÃO (TRI-ESTADO)
        if ($view === 'trash') {
            $query->onlyTrashed();
        }

        // 🔍 MOTOR DE BUSCA
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('document', 'like', "%{$search}%");
            });
        }

        // ↕️ MOTOR DE ORDENAÇÃO
        $customers = $query->orderBy($sort, $direction)
            ->paginate(15)
            ->withPath('/customers')
            ->withQueryString();

        $freeDevices = \App\Models\Device::whereNull('vehicle_id')->orderBy('internal_code')->get(['id', 'internal_code', 'imei']);
        $freeSims = \App\Models\GsmCard::whereDoesntHave('device')->orderBy('iccid')->get(['id', 'iccid', 'phone_number']);

        return view('customers.index', compact('customers', 'search', 'view', 'sort', 'direction', 'freeDevices', 'freeSims'));
    }

    public function dossier(Customer $customer)
    {
        $customer->load(['subUsers' => fn($q) => $q->withTrashed(), 'drivers' => fn($q) => $q->withTrashed()]);
        return response()->json($customer);
    }

    /**
     * Registro e Sincronização de Membros da Equipe
     */
    public function storeMember(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'role' => 'required|in:driver,operator',
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50',
            'password' => 'nullable|string|min:8',
            'document' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            'whatsapp' => 'nullable|string|max:25'
        ]);

        if ($data['role'] === 'driver') {
            // 🚛 GRAVAÇÃO MOTORISTA
            $member = $customer->drivers()->updateOrCreate(
                ['cnh_number' => $data['document']],
                [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'whatsapp' => $data['whatsapp'],
                    'status' => 'active'
                ]
            );
            
            // Criar acesso de portal se houver senha
            if ($data['password']) {
                $customer->subUsers()->updateOrCreate(
                    ['nickname' => $data['username']],
                    [
                        'name' => $data['name'],
                        'role' => 'driver',
                        'external_username' => $data['username'],
                        'external_password' => bcrypt($data['password']),
                        'email' => $data['email'],
                    ]
                );
            }
        } else {
            // 🛡️ GRAVAÇÃO OPERADOR (SUB-USUÁRIO)
            $member = $customer->subUsers()->updateOrCreate(
                ['nickname' => $data['username']],
                [
                    'name' => $data['name'],
                    'role' => 'operator',
                    'external_username' => $data['username'],
                    'external_password' => $data['password'] ? bcrypt($data['password']) : null,
                    'email' => $data['email'],
                    'whatsapp' => $data['whatsapp']
                ]
            );
        }

        return response()->json(['success' => true, 'message' => "Integrante {$data['name']} sincronizado!"]);
    }

    public function toggleMember(Request $request, Customer $customer, $memberId)
    {
        $role = $request->input('role');
        if ($role === 'driver') {
            $member = $customer->drivers()->withTrashed()->findOrFail($memberId);
        } else {
            $member = $customer->subUsers()->withTrashed()->findOrFail($memberId);
        }

        if ($member->trashed()) {
            $member->restore();
            $status = 'Ativado';
        } else {
            $member->delete();
            $status = 'Inativado';
        }
        
        return response()->json(['success' => true, 'message' => "Integrante $status com sucesso!"]);
    }

    public function destroy(Customer $customer)
    {
        // REGRA DE OURO RASTERTECH: Proibido inativar se houver qualquer vínculo
        $hasVehicles = $customer->vehicles()->count() > 0;
        $hasDevices = $customer->devices()->count() > 0;
        $hasChips = $customer->gsmCards()->count() > 0;
        $teamCount = $customer->subUsers()->count() + $customer->drivers()->count();
        $hasTeam = $teamCount > 0;

        if ($hasVehicles || $hasDevices || $hasChips || $hasTeam) {
            $parts = [];
            if ($hasVehicles) $parts[] = $customer->vehicles()->count() . " veículo(s)";
            if ($hasDevices) $parts[] = $customer->devices()->count() . " unidade(s) rastreada(s)";
            if ($hasChips) $parts[] = $customer->gsmCards()->count() . " chip(s)";
            if ($hasTeam) $parts[] = $teamCount . " membro(s) de equipe";
            
            $text = "Você não pode inativar este cliente, pois ele possui " . implode(", ", $parts) . " ativos.";
            return redirect()->back()->with('warning_block', $text);
        }

        $customer->delete();
        return redirect()->route('customers.index')->with('success', "Cliente inativado com sucesso.");
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:100',
            'company_name' => 'nullable|string|max:200',
            'email' => 'required|string|max:255',
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
            'email' => 'required|string|max:255',
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
