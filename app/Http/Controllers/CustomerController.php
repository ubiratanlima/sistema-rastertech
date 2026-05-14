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
            $searchLower = strtolower($search);
            $query->where(function($q) use ($searchLower) {
                $q->where(DB::raw('LOWER(name)'), 'like', "%{$searchLower}%")
                  ->orWhere(DB::raw('LOWER(company_name)'), 'like', "%{$searchLower}%")
                  ->orWhere(DB::raw('LOWER(document)'), 'like', "%{$searchLower}%");
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
            'role' => 'required|in:driver,operator,autorizado',
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
                \App\Models\User::updateOrCreate(
                    ['external_username' => $data['username']],
                    [
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'password' => bcrypt($data['password']),
                        'role' => 'Motorista',
                        'customer_id' => $customer->id,
                        'gender' => 'Masculino'
                    ]
                );
            }
        } else {
            // 🛡️ GRAVAÇÃO OPERADOR OU AUTORIZADO (SUB-USUÁRIO)
            $customer->subUsers()->updateOrCreate(
                ['external_username' => $data['username']],
                [
                    'name' => $data['name'],
                    'role' => $data['role'],
                    'external_username' => $data['username'],
                    'external_password' => $data['password'] ? bcrypt($data['password']) : null,
                    'email' => $data['email'],
                ]
            );

            // Sincroniza com a tabela principal de Users para permitir Login
            if ($data['password']) {
                \App\Models\User::updateOrCreate(
                    ['external_username' => $data['username']],
                    [
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'password' => bcrypt($data['password']),
                        'role' => ucfirst($data['role']), // 'Operator' ou 'Autorizado'
                        'customer_id' => $customer->id,
                        'gender' => 'Masculino'
                    ]
                );
            }
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
            // Restaura também o usuário principal se houver
            \App\Models\User::withTrashed()->where('external_username', $member->external_username)->restore();
            $status = 'Ativado';
        } else {
            $member->delete();
            // Inativa também o usuário principal se houver
            \App\Models\User::where('external_username', $member->external_username)->delete();
            $status = 'Inativado';
        }
        
        return response()->json(['success' => true, 'message' => "Integrante $status com sucesso!"]);
    }

    public function destroy(Customer $customer)
    {
        // REGRA DE OURO RASTERTECH: Proibido inativar se houver ATIVOS FÍSICOS/HARDWARE
        $hasVehicles = $customer->vehicles()->count() > 0;
        $hasDevices = $customer->devices()->count() > 0;
        $hasChips = $customer->gsmCards()->count() > 0;

        if ($hasVehicles || $hasDevices || $hasChips) {
            $parts = [];
            if ($hasVehicles) $parts[] = $customer->vehicles()->count() . " veículo(s)";
            if ($hasDevices) $parts[] = $customer->devices()->count() . " unidade(s) rastreada(s)";
            if ($hasChips) $parts[] = $customer->gsmCards()->count() . " chip(s)";
            
            $text = "Você não pode inativar este cliente, pois ele possui " . implode(", ", $parts) . " ativos pendentes de desvínculo.";
            return redirect()->back()->with('warning_block', $text);
        }

        // DESATIVAÇÃO EM CASCATA DE SUB-USUÁRIOS E MOTORISTAS (Pessoas/Acessos)
        DB::transaction(function() use ($customer) {
            // Desativa membros da equipe
            $customer->subUsers()->delete();
            $customer->drivers()->delete();
            
            // Desativa todos os usuários de login vinculados a este cliente
            \App\Models\User::where('customer_id', $customer->id)->delete();
            
            $customer->delete();
        });

        return redirect()->route('customers.index')->with('success', "Cliente e todos os seus vínculos de acesso foram inativados com sucesso.");
    }

    public function store(Request $request, \App\Services\AsaasService $asaas)
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
            'state' => 'nullable|string|max:2',
            'notes' => 'nullable|string',
            'vehicles' => 'nullable|string',
            'v_plate' => 'nullable|string|max:10',
            'v_brand' => 'nullable|string|max:50',
            'v_model' => 'nullable|string|max:50'
        ];

        $validated = $request->validate($rules);
        
        // --- 🛰️ INTEGRAÇÃO AUTOMÁTICA ASAAS ---
        $asaasId = null;
        $securityCode = $validated['code'] ?? null;

        if ($request->filled('document')) {
            $documentClean = preg_replace('/[^0-9]/', '', $validated['document']);
            
            // Enviar para o Asaas
            $asaasResponse = $asaas->createCustomer([
                'name' => $validated['name'],
                'cpfCnpj' => $documentClean,
                'email' => explode(',', $validated['email'])[0], // Primeiro e-mail se houver vários
                'groupName' => 'RASTERTECH',
                'mobilePhone' => $validated['cell_phone'] ?? null,
                'postalCode' => $validated['zip_code'] ?? null,
                'address' => $validated['street'] ?? null,
                'addressNumber' => $validated['number'] ?? null,
                'province' => $validated['neighborhood'] ?? null,
                'externalReference' => 'RT_NEW_' . time()
            ]);

            if ($asaasResponse && isset($asaasResponse['id'])) {
                $asaasId = $asaasResponse['id'];
                $securityCode = str_replace('cus_', '', $asaasId);
            }
        }

        DB::transaction(function() use ($validated, $request, $asaasId, $securityCode) {
            $customerData = collect($validated)->except(['vehicles', 'v_plate', 'v_brand', 'v_model'])->toArray();
            
            // Sobrescreve campos com dados do Asaas
            $customerData['asaas_id'] = $asaasId;
            $customerData['code'] = $securityCode;
            $customerData['origin'] = 'ASAAS';
            $customerData['asaas_group'] = 'RASTERTECH';

            $customer = Customer::create($customerData);
            
            // 🚛 INTEGRAÇÃO TÁTICA: Se vier um veículo no wizard, salva na frota
            if ($request->filled('v_plate')) {
                $customer->vehicles()->create([
                    'plate' => strtoupper(trim($request->v_plate)),
                    'brand' => $request->v_brand ?? '---',
                    'model' => $request->v_model ?? '---',
                ]);
            }
            
            // Suporte legado para JSON se houver
            if ($request->filled('vehicles')) {
                $vehicles = json_decode($request->vehicles, true);
                if (is_array($vehicles)) {
                    foreach ($vehicles as $v) {
                        if (!empty($v['plate'])) {
                            $customer->vehicles()->firstOrCreate(
                                ['plate' => strtoupper(trim($v['plate']))],
                                [
                                    'brand' => $v['brand'] ?? '---',
                                    'model' => $v['model'] ?? '---',
                                ]
                            );
                        }
                    }
                }
            }
        });

        return redirect()->route('customers.index')->with('success', "Cliente e frota inicial registrados com sucesso.");
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
