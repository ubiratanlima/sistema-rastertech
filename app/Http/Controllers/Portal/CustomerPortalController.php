<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PortalDriver;
use App\Models\VehicleChecklist;
use App\Models\VehicleMission;
use App\Models\VehicleExpense;
use App\Models\CustomerWhatsappNumber;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerPortalController extends Controller
{
    /**
     * DASHBOARD PRINCIPAL DO CLIENTE
     */
    public function index(Request $request)
    {
        // ⚓ CONTEXTO OPERACIONAL E SEGURANÇA
        $user = Auth::user();
        $userRole = strtolower($user->role);
        $isAdminLevel = in_array($userRole, ['admin', 'gerente', 'operador', 'administrador', 'gestor']);

        if (!$isAdminLevel) {
            // SE FOR CLIENTE: Trava no ID dele e limita lista de customers
            $selectedCustomerId = $user->customer_id;
            $customers = \App\Models\Customer::where('id', $selectedCustomerId)->get();
        } else {
            // SE FOR ADMIN: Mantém seletor global
            $customers = \App\Models\Customer::orderBy('name')->get();
            $selectedCustomerId = $request->get('customer_id', session('portal_customer_id'));
        }
        
        if ($selectedCustomerId) {
            session(['portal_customer_id' => $selectedCustomerId]);
            $customer = \App\Models\Customer::find($selectedCustomerId);
        } else {
            $customer = $customers->first(); 
            if ($customer) {
                session(['portal_customer_id' => $customer->id]);
            }
        }

        if (!$customer && !$isAdminLevel) {
            return redirect('/')->with('error', 'Não foi possível localizar um cliente vinculado ao seu perfil.');
        }

        $stats = [
            'drivers_count' => PortalDriver::where('customer_id', $customer->id)->count(),
            'vehicles_count' => Vehicle::where('customer_id', $customer->id)->count(),
            'checklists_count' => VehicleChecklist::whereHas('vehicle', function($q) use ($customer) {
                $q->where('customer_id', $customer->id);
            })->count()
        ];

        return view('portal.dashboard', compact('customer', 'customers', 'stats', 'isAdminLevel'));
    }

    /**
     * CARREGAMENTO DINÂMICO DE COMPONENTES (PWA STYLE)
     */
    public function loadComponent(Request $request, $component)
    {
        $user = Auth::user();
        $userRole = strtolower($user->role);
        $isAdminLevel = in_array($userRole, ['admin', 'gerente', 'operador', 'administrador', 'gestor']);

        // Se for cliente, ignora o ID da request e força o dele
        if (!$isAdminLevel) {
            $customerId = $user->customer_id;
        } else {
            $customerId = $request->get('customer_id', session('portal_customer_id'));
        }
        
        $customer = \App\Models\Customer::find($customerId);

        if (!$customer) {
            return response()->json(['error' => 'É necessário selecionar um cliente.'], 403);
        }

        switch ($component) {
            case 'veiculos':
                $vehicles = Vehicle::where('customer_id', $customer->id)->get();
                return view('portal.components.veiculos', compact('vehicles'));
            
            case 'motoristas':
                $drivers = PortalDriver::where('customer_id', $customer->id)->orderBy('updated_at', 'desc')->get();
                return view('portal.components.motoristas', compact('drivers'));
            
            case 'perfil':
                if (strtolower($user->role) === 'autorizado') {
                    return response()->json(['error' => 'Acesso negado: Somente o Cliente titular pode gerenciar o perfil e WhatsApp.'], 403);
                }
                $whatsapps = \App\Models\CustomerWhatsappNumber::where('customer_id', $customer->id)->get();
                $sectors = \Illuminate\Support\Facades\DB::table('whatsapp_sectors')->orderBy('name')->get();
                return view('portal.components.perfil', compact('customer', 'whatsapps', 'sectors'));
            
            case 'suporte':
                return view('portal.components.suporte', compact('customer'));

            case 'checklist':
                $vehicle = Vehicle::findOrFail($request->id);
                return view('portal.components.checklist', compact('vehicle'));

            default:
                return response()->json(['error' => 'Componente não encontrado'], 404);
        }
    }

    /**
     * GESTÃO DE PERFIL E WHATSAPP
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        if (strtolower($user->role) === 'autorizado') {
            return redirect()->back()->with('error', 'Acesso negado: Você não tem permissão para alterar os dados do cliente.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'image' => 'nullable|image|max:5120'
        ];

        $request->validate($rules);


        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('image')) {
            if ($user->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->image);
            }
            $user->image = $request->file('image')->store('users/images', 'public');
        }

        $user->save();

        return redirect()->back()->with('success', 'Perfil atualizado com sucesso!');
    }

    public function addWhatsapp(Request $request)
    {
        $user = Auth::user();
        if (strtolower($user->role) === 'autorizado') {
            return response()->json(['error' => 'Acesso negado: Você não tem permissão para gerenciar contatos de WhatsApp.'], 403);
        }

        $customerId = session('portal_customer_id') ?? 1;

        // 🛑 LIMITE OPERACIONAL: Máximo 20 números por cliente
        $count = \App\Models\CustomerWhatsappNumber::where('customer_id', $customerId)->count();
        if ($count >= 20) {
            return response()->json(['error' => 'Limite de 20 números atingido para este cliente.'], 422);
        }

        $request->validate([
            'number' => 'required',
            'label' => 'required',
            'contact_name' => 'nullable|string|max:100'
        ]);

        $label = mb_strtoupper($request->label);

        // 🏗️ GESTÃO DE NOVO SETOR (Se não existir)
        if ($request->is_new_sector) {
            \Illuminate\Support\Facades\DB::table('whatsapp_sectors')->insertOrIgnore([
                'name' => $label,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        \App\Models\CustomerWhatsappNumber::create([
            'customer_id' => $customerId,
            'whatsapp_number' => $request->number,
            'label' => $label,
            'contact_name' => $request->contact_name
        ]);

        return response()->json(['success' => 'WhatsApp cadastrado com sucesso!']);
    }

    public function deleteWhatsapp($id)
    {
        $user = Auth::user();
        if (strtolower($user->role) === 'autorizado') {
            return response()->json(['error' => 'Acesso negado.'], 403);
        }

        $customerId = session('portal_customer_id') ?? 1;
        \App\Models\CustomerWhatsappNumber::where('id', $id)->where('customer_id', $customerId)->delete();
        return response()->json(['success' => 'WhatsApp removido!']);
    }

    /**
     * GESTÃO DE MOTORISTAS E CNH (PROCESSO TÁTICO FINAL)
     */
    public function saveDriver(Request $request)
    {
        try {
            $user = Auth::user();
            $userRole = strtolower($user->role);
            $isAdminLevel = in_array($userRole, ['admin', 'gerente', 'suporte', 'administrador', 'gestor']);

            // 🛡️ DEFINIÇÃO DO CUSTOMER_ID (Prioridade: Usuário Logado > Sessão > Default)
            if (!$isAdminLevel) {
                $customerId = $user->customer_id;
            } else {
                $customerId = session('portal_customer_id') ?? 1;
            }

            // 🛡️ MODO OPERACIONAL: SE FOR UMA ATUALIZAÇÃO SÓ DE FOTO (DIRECT UPLOAD)
            if ($request->has('driver_id') && !$request->has('name')) {
                $request->validate([
                    'driver_id' => 'required|exists:portal_drivers,id',
                    'cnh_front' => 'nullable|image|max:20480',
                    'cnh_back' => 'nullable|image|max:20480'
                ]);

                $driver = \App\Models\PortalDriver::findOrFail($request->driver_id);
            } else {
                // 📝 MODO OPERACIONAL: FORMULÁRIO COMPLETO
                $request->validate([
                    'driver_id' => 'nullable|exists:portal_drivers,id',
                    'name' => 'required|string|max:150',
                    'email' => 'required|email',
                    'cpf' => 'required|string|max:20',
                    'cnh_number' => 'required|string|max:25',
                    'issue_date' => 'required|date',
                    'cnh_expiry' => 'required|date',
                    'cnh_front' => 'nullable|image|max:51200',
                    'cnh_back' => 'nullable|image|max:51200',
                    'external_password' => 'nullable|min:4'
                ]);

                $data = $request->except(['cnh_front', 'cnh_back', 'issuer_uf', 'driver_id', '_token']);
                $data['customer_id'] = $customerId;
                
                // Senha padrão se não informada
                if (empty($data['external_password'])) {
                    $data['external_password'] = '123456';
                }

                // 🏗️ TRATAMENTO DE ÓRGÃO EMISSOR (SSP/SP)
                if ($request->issuer_uf) {
                    $parts = explode('/', $request->issuer_uf);
                    $data['issuer'] = trim($parts[0] ?? 'SSP');
                    $data['uf'] = trim($parts[1] ?? 'SP');
                }

                $driver = \App\Models\PortalDriver::updateOrCreate(
                    ['id' => $request->driver_id],
                    $data
                );

                // 🔗 SINCRONIZAÇÃO DE CREDENCIAIS (Raiz)
                // Se o motorista não tem sub_user, criamos um para ele aparecer em "Credenciais Apps"
                $platform = \App\Models\Platform::first(); // Pega a primeira plataforma como padrão
                
                $subUser = \App\Models\CustomerSubUser::updateOrCreate(
                    ['email' => $driver->email],
                    [
                        'customer_id' => $customerId,
                        'platform_id' => $platform ? $platform->id : 1,
                        'name' => $driver->name,
                        'role' => 'Motorista',
                        'external_username' => $driver->email,
                        'external_password' => $driver->external_password,
                        'access_validated' => true
                    ]
                );

                // Atualiza o vínculo no motorista
                $driver->update(['sub_user_id' => $subUser->id]);

                // Garante o registro na tabela de Users para login
                \App\Models\User::updateOrCreate(
                    ['external_username' => $subUser->external_username],
                    [
                        'name' => $subUser->name,
                        'email' => $subUser->email,
                        'password' => \Illuminate\Support\Facades\Hash::make($subUser->external_password),
                        'role' => 'Motorista',
                        'customer_id' => $subUser->customer_id,
                        'access_validated' => true,
                        'external_username' => $subUser->external_username,
                        'external_password' => $subUser->external_password
                    ]
                );
            }

            // 📸 GESTÃO DE ARQUIVOS (NOMENCLATURA PROFISSIONAL RASTERTECH)
            if ($request->hasFile('cnh_front')) {
                $file = $request->file('cnh_front');
                $extension = strtolower($file->getClientOriginalExtension());
                $fileName = "motorista_{$driver->id}_FRENTE.{$extension}";
                $path = $file->storeAs('drivers', $fileName, 'public');
                $driver->update(['cnh_front_path' => $path]);
            }

            if ($request->hasFile('cnh_back')) {
                $file = $request->file('cnh_back');
                $extension = strtolower($file->getClientOriginalExtension());
                $fileName = "motorista_{$driver->id}_VERSO.{$extension}";
                $path = $file->storeAs('drivers', $fileName, 'public');
                $driver->update(['cnh_back_path' => $path]);
            }

            return response()->json([
                'success' => true, 
                'message' => 'Dados sincronizados com a Central Rastertech!',
                'driver_id' => $driver->id
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'error' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('❌ [PORTAL] ERRO NO UPLOAD TÁTICO: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Falha interna na central ao processar imagem.'], 500);
        }
    }

    /**
     * REGISTRO DE CHECKLIST (ANTIGO - COMPATIBILIDADE)
     */
    public function storeChecklist(Request $request)
    {
        return response()->json(['success' => 'Checklist registrado com sucesso!']);
    }

    /**
     * 🚜 DASHBOARD DO MOTORISTA (VERIFICAÇÕES)
     */
    public function verificacoes(Request $request)
    {
        // 🛡️ IDENTIFICAÇÃO TÁTICA DO MOTORISTA (OU ADMIN)
        $user = Auth::user();
        
        // Papéis que possuem visão de supervisão (vêem todos os motoristas do cliente)
        $supervisorRoles = ['Administrador', 'Gerente', 'Suporte', 'Cliente', 'Autorizado'];
        $isSupervisor = in_array($user->role, $supervisorRoles);

        $subUser = \App\Models\CustomerSubUser::where('external_username', $user->external_username)->first();
        $driver = null;

        if ($subUser) {
            $driver = PortalDriver::where('sub_user_id', $subUser->id)->first();
        }

        // Se não for supervisor e não tiver perfil de motorista, redireciona
        if (!$isSupervisor && (!$subUser || !$driver)) {
            return redirect()->route('dashboard')->with('error', 'Seu acesso não está vinculado a um perfil de Portal/Motorista.');
        }

        // 📊 QUERY DE BUSCA - FOCO EM MISSÕES (Dossiê Unificado)
        $query = VehicleMission::with(['vehicle', 'driver', 'entryChecklist', 'exitChecklist', 'customer']);
        
        if ($isSupervisor) {
            // 🛡️ ISOLAMENTO MULTI-TENANT: Se for Cliente ou Autorizado, filtra pelo seu ID
            if (in_array($user->role, ['Cliente', 'Autorizado'])) {
                $query->where('customer_id', $user->customer_id);
            }
            // Admin, Gerente e Suporte (Rastertech) possuem visão global
        } else {
            // Motorista vê apenas as missões iniciadas por ele
            $query->where('driver_id', $driver->id);
        }

        $checklists = $query->orderBy('created_at', 'desc')->paginate(15);

        // 📊 ESTADO DA JORNADA ATUAL (Último Registro para o Dashboard)
        $lastChecklist = null;
        if ($isSupervisor && in_array($user->role, ['Cliente', 'Autorizado'])) {
            // Para o Cliente, 'isOnline' significa se existe alguém em campo na frota dele
            $isOnline = VehicleMission::where('customer_id', $user->customer_id)->whereNull('exit_id')->exists();
        } else {
            $stateDriver = $driver ?: PortalDriver::where('customer_id', $user->customer_id)->first();
            $lastChecklist = $stateDriver ? VehicleChecklist::where('driver_id', $stateDriver->id)->orderBy('created_at', 'desc')->first() : null;
            $isOnline = ($lastChecklist && $lastChecklist->type == 'entry');
        }

        // 📊 HISTÓRICO IMUTÁVEL (Últimos 30 dias)
        $checklists = $query->orderBy('created_at', 'desc')->paginate(10);

        // Papéis que possuem visão de supervisão (vêem todos os motoristas do cliente)
        $supervisorRoles = ['Administrador', 'Gerente', 'Suporte', 'Cliente', 'Autorizado'];
        $isSupervisor = in_array($user->role, $supervisorRoles);

        return view('portal.verificacoes.index', compact('driver', 'checklists', 'lastChecklist', 'isOnline', 'isSupervisor'));
    }

    /**
     * 📝 FORMULÁRIO DE NOVA VERIFICAÇÃO (CHECK-IN / OUT)
     */
    public function createChecklist(Request $request, $type)
    {
        $user = Auth::user();
        $isSupervisor = in_array($user->role, ['Administrador', 'Gerente', 'Suporte', 'Cliente', 'Autorizado']);

        $subUser = \App\Models\CustomerSubUser::where('external_username', $user->external_username)->first();
        $driver = $subUser ? PortalDriver::where('sub_user_id', $subUser->id)->first() : null;

        if (!$isSupervisor && (!$subUser || !$driver)) {
            return redirect()->route('dashboard')->with('error', 'Ação permitida apenas para motoristas autorizados.');
        }

        // 🛡️ VALIDAÇÃO TÁTICA DE POSSE DE JORNADA (ESTILO CUSTÓDIA)
        $isOnline = false;
        $activeJourney = null;

        // Se for um motorista comum, sua jornada pessoal ainda importa (ele só pode estar em um veículo por vez)
        if (!$isSupervisor && $driver) {
            $personalLast = VehicleChecklist::where('driver_id', $driver->id)->orderBy('created_at', 'desc')->first();
            if ($type == 'entry' && $personalLast && $personalLast->type == 'entry') {
                return redirect()->route('portal.verificacoes.index')->with('warning', 'Você já possui um Check-in ativo em outro veículo. Realize o Check-out antes.');
            }
        }

        // 🚛 TRAVA DE ATIVO (BLOQUEIO DE VEÍCULO EM USO)
        // No Check-out, precisamos encontrar se EXISTE uma jornada aberta para decidir se o formulário abre
        if ($type == 'exit') {
            $exitQuery = VehicleChecklist::where('type', 'entry');

            // 🎯 BUG FIX: Se vier vehicle_id na request (ex: botão CHECKOUT da tela de show),
            // usamos ele para garantir que o supervisor trate o veículo correto.
            if ($request->filled('vehicle_id')) {
                $exitQuery->where('vehicle_id', $request->vehicle_id);
            } elseif (!$isSupervisor) {
                // Motorista comum só pode dar saída no que ele mesmo abriu
                $exitQuery->where('driver_id', $driver?->id);
            }

            $activeJourney = $exitQuery->orderBy('created_at', 'desc')->first();
            
            // Verificamos se esse 'entry' já foi fechado
            if ($activeJourney) {
                $hasExit = VehicleChecklist::where('vehicle_id', $activeJourney->vehicle_id)
                    ->where('type', 'exit')
                    ->where('created_at', '>', $activeJourney->created_at)
                    ->exists();
                
                if (!$hasExit) {
                    $isOnline = true;
                }
            }

            if (!$isOnline) {
                $msg = 'Não há nenhuma jornada ativa disponível para Check-out.';
                return redirect()->route('portal.verificacoes.index')->with('warning', $msg);
            }
        }

        // 🛡️ Papéis com visão gerencial (Global ou do Próprio Cliente)
        $isGlobal = in_array($user->role, ['Administrador', 'Gerente', 'Suporte']);
        $isTenant = in_array($user->role, ['Cliente', 'Autorizado']);

        // 🚛 VEÍCULOS DISPONÍVEIS NA FROTA DO CLIENTE
        if ($type == 'exit' && $activeJourney) {
            $vehicles = Vehicle::where('customer_id', $activeJourney->vehicle->customer_id)->get();
        } elseif ($isGlobal) {
            $vehicles = Vehicle::orderBy('plate')->get(); // Global vê todos
        } else {
            // Cliente ou Motorista: vê apenas a frota do cliente dele
            $targetCustomerId = $isTenant ? $user->customer_id : ($driver ? $driver->customer_id : null);
            $vehicles = Vehicle::where('customer_id', $targetCustomerId)->get();
        }
        
        // 🎯 PRÉ-SELEÇÃO DO VEÍCULO E GARANTIA NA LISTA
        $currentVehicleId = ($type == 'exit' && $activeJourney) ? $activeJourney->vehicle_id : null;
        if ($currentVehicleId && !$vehicles->contains('id', $currentVehicleId)) {
            $vehicles->push($activeJourney->vehicle);
        }

        // 🛡️ IDENTIFICAÇÃO DE OCUPAÇÃO (TRANSPARÊNCIA) - Para os veículos que não estão com o motorista atual
        foreach ($vehicles as $v) {
            $v->is_locked = false;
            // Busca o último registro de 'entry' para este veículo para ver se ele está "em trânsito"
            $lockCheck = VehicleChecklist::where('vehicle_id', $v->id)->orderBy('created_at', 'desc')->first();
            
            if ($lockCheck && $lockCheck->type == 'entry') {
                $v->is_locked = true;
                $v->locked_by_id = $lockCheck->driver_id;
                $v->locked_by_name = $lockCheck->driver->name ?? 'Sistema';
                $v->locked_at = $lockCheck->created_at->format('d/m H:i');
            }
        }

        
        // Se for Supervisor em Check-out, o "motorista" do formulário deve ser o dono da jornada ativa (se houver)
        if ($isSupervisor && $type == 'exit' && $activeJourney) {
            $driver = $activeJourney->driver;
        }

        // 🛡️ BUSCA DE ÚLTIMO KM (PARA REFERÊNCIA NO FORMULÁRIO)
        $lastRecord = VehicleChecklist::where('vehicle_id', $currentVehicleId)->orderBy('created_at', 'desc')->first();
        $last_odometer = $lastRecord ? $lastRecord->odometer : 0;

        // 👥 LISTA DE MOTORISTAS PARA SUPERVISORES (Check-in manual por ADM/Autorizado)
        $drivers = [];
        if ($isSupervisor) {
            $driversQuery = PortalDriver::activeAndValid();
            if (!$isGlobal) {
                $driversQuery->where('customer_id', $user->customer_id);
            }
            $drivers = $driversQuery->orderBy('name')->get();
        }

        return view('portal.verificacoes.form', compact('driver', 'vehicles', 'type', 'currentVehicleId', 'isSupervisor', 'activeJourney', 'last_odometer', 'drivers'));
    }

    /**
     * 💾 PERSISTÊNCIA TÁTICA DE JORNADA (10 FOTOS)
     */
    public function storeChecklistAction(Request $request)
    {
        $user = Auth::user();
        $supervisorRoles = ['Administrador', 'Gerente', 'Suporte', 'Cliente', 'Autorizado'];
        $isAdmin = in_array($user->role, $supervisorRoles);

        // 🛡️ REGRAS DE VALIDAÇÃO DINÂMICAS
        $rules = [
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:portal_drivers,id',
            'type' => 'required|in:entry,exit',
            'odometer' => 'required|numeric|min:0',
            'notes' => 'required|string|min:15|max:500',
        ];

        // Bypass para Admins/Gestores/Operadores: fotos opcionais em Check-in e Check-out para testes/uso ADM
        $photoRule = $isAdmin ? 'nullable' : 'required';

        for ($i = 1; $i <= 5; $i++) {
            $rules["photo_$i"] = "$photoRule|image|max:10240";
        }

        // Fotos extras são sempre opcionais
        for ($i = 6; $i <= 10; $i++) {
            $rules["photo_$i"] = "nullable|image|max:10240";
        }

        // Se for Admin dando baixa ou checkin, reduzimos a exigência de notas longas para facilitar fluxos
        if ($isAdmin) {
            $rules['notes'] = 'nullable|string|max:1000';
        }

        $request->validate($rules, [
            'notes.min' => 'O Relato deve conter pelo menos 15 caracteres.',
            'photo_1.required' => 'A foto do Odômetro é obrigatória.',
            'photo_2.required' => 'A foto da Frente é obrigatória.',
            'photo_3.required' => 'A foto da Traseira é obrigatória.',
            'photo_4.required' => 'A foto da Lateral Direita é obrigatória.',
            'photo_5.required' => 'A foto da Lateral Esquerda é obrigatória.',
        ]);

        // 🛡️ VALIDAÇÃO DE PROPRIEDADE (SEGURANÇA TÁTICA)
        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $isGlobal = in_array($user->role, ['Administrador', 'Gerente', 'Suporte']);
        
        if (!$isGlobal) {
            $ownerId = $user->customer_id;
            if (!$ownerId) {
                $subUser = \App\Models\CustomerSubUser::where('external_username', $user->external_username)->first();
                $driverProfile = $subUser ? PortalDriver::where('sub_user_id', $subUser->id)->first() : null;
                $ownerId = $driverProfile ? $driverProfile->customer_id : null;
            }

            if ($vehicle->customer_id != $ownerId) {
                abort(403, 'Acesso Negado: Este veículo não pertence à sua frota.');
            }
        }

        // 🛡️ VALIDAÇÃO DE CONTINUIDADE DE ODÔMETRO (HODÔMETRO TÁTICO)
        $lastCheck = VehicleChecklist::where('vehicle_id', $request->vehicle_id)->orderBy('created_at', 'desc')->first();
        $isSupervisor = in_array($user->role, ['Administrador', 'Gerente', 'Suporte', 'Cliente', 'Autorizado']);
        
        if ($lastCheck) {
            $lastKm = $lastCheck->odometer;
            $currentKm = (int) $request->odometer;

            if ($request->type == 'entry') {
                // Check-in deve ser IGUAL ao último Checkout
                if ($currentKm != $lastKm && !$isSupervisor) {
                    return back()->withInput()->with('error', "O KM de entrada ({$currentKm}) deve ser exatamente igual ao KM do último checkout ({$lastKm}).");
                }
            } else {
                // Check-out deve ser MAIOR ou IGUAL ao Check-in
                if ($currentKm < $lastKm && !$isSupervisor) {
                    return back()->withInput()->with('error', "O KM de saída ({$currentKm}) não pode ser menor que o KM de entrada ({$lastKm}).");
                }
            }

            // ⚠️ BYPASS SUPERVISOR: Se houver divergência, exige justificativa robusta
            if ($isSupervisor && $currentKm != $lastKm && strlen($request->notes) < 30) {
                 return back()->withInput()->with('error', "Divergência de KM detectada ({$currentKm} vs {$lastKm}). Como Supervisor, você deve justificar o motivo no campo de mensagens (mínimo 30 caracteres).");
            }
        }

        // 🚛 DADOS DO ATIVO E CONTEXTO
        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $date = now()->format('Y-m-d');
        $photos = [];

        // 📂 PROCESSAMENTO DE SLOTS DE IMAGEM
        for ($i = 1; $i <= 10; $i++) {
            if ($request->hasFile("photo_$i")) {
                $file = $request->file("photo_$i");
                $fileName = "checklist_{$request->type}_{$i}_" . time() . ".{$file->getClientOriginalExtension()}";
                $path = $file->storeAs("checklists/{$request->driver_id}/{$date}", $fileName, 'public');
                $photos["photo_$i"] = $path;
            }
        }

        // 💾 SALVAMENTO COM TRILHA DE AUDITORIA
        $checklist = VehicleChecklist::create([
            'customer_id'     => $vehicle->customer_id,
            'vehicle_id'      => $request->vehicle_id,
            'driver_id'       => $request->driver_id,
            'performed_by_id' => $user->id, // Quem deu o clique
            'type'            => $request->type,
            'odometer'        => $request->odometer,
            'fuel_level'      => $request->fuel_level ?? 'N/A',
            'photos'          => $photos,
            'notes'           => $request->notes
        ]);

        // 🔄 SINCRONIZAÇÃO DE ESTADO DO VEÍCULO (HODÔMETRO TÁTICO)
        if ($request->type == 'entry') {
            $vehicle->update([
                'is_locked' => true,
                'last_checklist_id' => $checklist->id
            ]);
        } else {
            $vehicle->update([
                'is_locked' => false,
                // Mantemos o last_checklist_id opcionalmente ou limpamos
            ]);
        }

        // 🆙 ATUALIZAÇÃO DE STATUS NO MOTORISTA
        $driver = PortalDriver::find($request->driver_id);
        if ($driver) {
            $driver->update(['last_checklist_at' => now()]);
        }

        // 🎖️ GESTÃO TÁTICA DE MISSÕES (PONTO DE UNIÃO)
        if ($request->type == 'entry') {
            // Check-in: Iniciamos uma nova Missão
            VehicleMission::create([
                'customer_id' => $vehicle->customer_id,
                'vehicle_id'  => $request->vehicle_id,
                'driver_id'   => $request->driver_id,
                'entry_id'    => $checklist->id,
                'status'      => 'open'
            ]);
        } else {
            // Check-out: Buscamos a missão aberta deste veículo para fechá-la
            // ⚠️ BUG FIX: Removido filtro por customer_id — o vehicle_id + status 'open'
            // já é suficiente e evita falhas quando o operador tem customer_id diferente do motorista.
            $openMission = VehicleMission::where('vehicle_id', $request->vehicle_id)
                ->where('status', 'open')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($openMission) {
                $openMission->update([
                    'exit_id' => $checklist->id,
                    'status'  => 'closed'
                ]);
            } else {
                // Caso não haja missão aberta (ex: checkout administrativo), criamos uma missão já fechada
                VehicleMission::create([
                    'customer_id' => $vehicle->customer_id,
                    'vehicle_id'  => $request->vehicle_id,
                    'driver_id'   => $request->driver_id,
                    'entry_id'    => null, // Sem entrada vinculada
                    'exit_id'     => $checklist->id,
                    'status'      => 'closed'
                ]);
            }
        }

        return redirect()->route('portal.verificacoes.index')
            ->with('success', 'Verificação (' . strtoupper($request->type) . ') registrada com sucesso!');
    }

    /**
     * 🔍 BUSCA DINÂMICA DE ÚLTIMO ODÔMETRO (AJAX)
     */
    public function getLastOdometer($vehicle_id)
    {
        $user = Auth::user();
        $vehicle = Vehicle::findOrFail($vehicle_id);
        $isGlobal = in_array($user->role, ['Administrador', 'Gerente', 'Suporte']);

        if (!$isGlobal) {
            $ownerId = $user->customer_id;
            if (!$ownerId) {
                $subUser = \App\Models\CustomerSubUser::where('external_username', $user->external_username)->first();
                $driverProfile = $subUser ? PortalDriver::where('sub_user_id', $subUser->id)->first() : null;
                $ownerId = $driverProfile ? $driverProfile->customer_id : null;
            }

            if ($vehicle->customer_id != $ownerId) {
                return response()->json(['success' => false, 'message' => 'Veículo inválido para sua frota.'], 403);
            }
        }

        $lastRecord = VehicleChecklist::where('vehicle_id', $vehicle_id)->orderBy('created_at', 'desc')->first();
        return response()->json([
            'success' => true,
            'odometer' => $lastRecord ? $lastRecord->odometer : 0
        ]);
    }

    /**
     * 👁️ VISUALIZAÇÃO DE HISTÓRICO (SOMENTE LEITURA)
     */
    public function showChecklist($id)
    {
        $checklist = VehicleChecklist::with(['vehicle', 'driver', 'performedBy'])->findOrFail($id);
        return view('portal.verificacoes.show', compact('checklist'));
    }

    /**
     * 💰 DASHBOARD DE DESPESAS (LISTAGEM)
     */
    public function despesas(Request $request)
    {
        $user = Auth::user();
        // 🛡️ Papéis com visão gerencial (Global ou do Próprio Cliente)
        $isGlobal = in_array($user->role, ['Administrador', 'Gerente', 'Suporte']);
        $isTenant = in_array($user->role, ['Cliente', 'Autorizado']);
        $isSupervisor = $isGlobal || $isTenant;

        $subUser = \App\Models\CustomerSubUser::where('external_username', $user->external_username)->first();
        $driver = $subUser ? PortalDriver::where('sub_user_id', $subUser->id)->first() : null;

        if (!$isSupervisor && !$driver) {
            return redirect()->route('dashboard')->with('error', 'Acesso negado: Perfil de motorista não encontrado.');
        }

        // 🔍 FILTROS
        $customerId = $request->input('customer_id');
        $vehicleId = $request->input('vehicle_id');
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');

        // 💰 HISTÓRICO DE DESPESAS
        $query = VehicleExpense::with(['vehicle.customer', 'driver']);
        
        if (!$isSupervisor) {
            // Motorista comum vê apenas as suas
            $query->where('driver_id', $driver->id);
        } else {
            // Gestores (Global ou Tenant)
            if ($isTenant) {
                // 🛡️ TRAVA MULTI-TENANT: Cliente vê apenas sua frota
                $query->whereHas('vehicle', function($q) use ($user) {
                    $q->where('customer_id', $user->customer_id);
                });
            } elseif ($customerId) {
                // Admin Global filtra se solicitado
                $query->whereHas('vehicle', function($q) use ($customerId) {
                    $q->where('customer_id', $customerId);
                });
            }
        }

        if ($vehicleId) {
            $query->where('vehicle_id', $vehicleId);
        }

        if ($dateStart) {
            $query->whereDate('created_at', '>=', $dateStart);
        }
        if ($dateEnd) {
            $query->whereDate('created_at', '<=', $dateEnd);
        }

        // CARGA TOTAL DO RELATÓRIO
        $expenses = $query->orderBy('created_at', 'desc')->get();
        $totalAmount = $expenses->sum('amount');

        // DADOS PARA OS FILTROS
        $customers = collect();
        $vehicles = collect();
        if ($isGlobal) {
            $customers = \App\Models\Customer::orderBy('name')->get();
            $vehicles = Vehicle::orderBy('plate')->get();
        } elseif ($isTenant) {
            $vehicles = Vehicle::where('customer_id', $user->customer_id)->orderBy('plate')->get();
        } elseif ($driver) {
            $vehicles = Vehicle::where('customer_id', $driver->customer_id)->orderBy('plate')->get();
        }

        $isAdmin = $isGlobal;
        $customer = $user->customer;
        
        return view('portal.despesas.index', compact(
            'driver', 'expenses', 'isSupervisor', 'isAdmin', 'customer', 'totalAmount', 
            'customers', 'vehicles', 'customerId', 'vehicleId', 'dateStart', 'dateEnd'
        ));
    }

    /**
     * 📝 FORMULÁRIO DE NOVA DESPESA
     */
    public function createDespesa(Request $request)
    {
        $user = Auth::user();
        $isGlobal = in_array($user->role, ['Administrador', 'Gerente', 'Suporte']);
        $isTenant = in_array($user->role, ['Cliente', 'Autorizado']);
        $isSupervisor = $isGlobal || $isTenant;

        $subUser = \App\Models\CustomerSubUser::where('external_username', $user->external_username)->first();
        $driver = $subUser ? PortalDriver::where('sub_user_id', $subUser->id)->first() : null;

        if (!$isSupervisor && !$driver) {
            return redirect()->route('dashboard')->with('error', 'Acesso negado.');
        }

        // Se for admin/gestor global, carrega toda a base de veículos
        if ($isGlobal) {
            $vehicles = Vehicle::orderBy('plate')->get();
        } else {
            // Cliente ou Motorista: vê apenas a frota do cliente dele
            $vehicles = Vehicle::where('customer_id', ($isTenant ? $user->customer_id : $driver->customer_id))->get();
        }

        // Categorias Padrão Ouro RTECH
        $categories = ['Abastecimento', 'Troca de Óleo', 'Manutenção', 'Lavagem', 'Pneus', 'Outros Gastos'];

        return view('portal.despesas.form', compact('driver', 'vehicles', 'categories'));
    }

    /**
     * 💾 PERSISTÊNCIA TÁTICA DA DESPESA
     */
    public function storeDespesaAction(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'nullable', // Permitir nulo para registros Admin
            'type' => 'required|string',
            'odometer' => 'required|numeric|min:0',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|min:3|max:100',
            'receipt_photo' => 'nullable|image|max:10240'
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $data = $request->except(['receipt_photo']);
        
        // 🛡️ TRATAMENTO DE SEGURANÇA: Se for Admin e o banco exigir um driver_id (NOT NULL)
        if ($data['driver_id'] == '0') {
            $fallbackDriver = PortalDriver::first();
            $data['driver_id'] = $fallbackDriver ? $fallbackDriver->id : null;
        }

        $entry = new VehicleExpense($data);
        $entry->customer_id = $vehicle->customer_id;

        // 📸 PROCESSAMENTO DE COMPROVANTE (OPCIONAL)
        if ($request->hasFile('receipt_photo')) {
            $path = $request->file('receipt_photo')->store('expenses/' . $request->driver_id, 'public');
            $entry->receipt_photo = $path;
        }

        $entry->save();

        return redirect()->route('portal.despesas.index')->with('success', '🛰️ DESPESA REGISTRADA COM SUCESSO! Registro ativo no dossiê do veículo.');
    }
}
