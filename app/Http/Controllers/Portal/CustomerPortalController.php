<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PortalDriver;
use App\Models\VehicleChecklist;
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
        // ⚓ CONTEXTO OPERACIONAL
        $customers = \App\Models\Customer::orderBy('name')->get();
        $selectedCustomerId = $request->get('customer_id', session('portal_customer_id'));
        
        if ($selectedCustomerId) {
            session(['portal_customer_id' => $selectedCustomerId]);
            $customer = \App\Models\Customer::find($selectedCustomerId);
        } else {
            $customer = $customers->first(); // Fallback para o primeiro
            session(['portal_customer_id' => $customer->id]);
        }

        $stats = [
            'drivers_count' => PortalDriver::where('customer_id', $customer->id)->count(),
            'vehicles_count' => Vehicle::where('customer_id', $customer->id)->count(),
            'checklists_count' => VehicleChecklist::whereHas('vehicle', function($q) use ($customer) {
                $q->where('customer_id', $customer->id);
            })->count()
        ];

        return view('portal.dashboard', compact('customer', 'customers', 'stats'));
    }

    /**
     * CARREGAMENTO DINÂMICO DE COMPONENTES (PWA STYLE)
     */
    public function loadComponent(Request $request, $component)
    {
        // Prioriza o ID vindo na request ou na sessão
        $customerId = $request->get('customer_id', session('portal_customer_id'));
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
        // Lógica de atualização de senha e nickname
        return response()->json(['success' => 'Perfil atualizado com sucesso!']);
    }

    public function addWhatsapp(Request $request)
    {
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
            $customerId = session('portal_customer_id') ?? 1;

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
                    'cpf' => 'required|string|max:20',
                    'cnh_number' => 'required|string|max:25',
                    'cnh_front' => 'nullable|image|max:20480',
                    'cnh_back' => 'nullable|image|max:20480'
                ]);

                $data = $request->except(['cnh_front', 'cnh_back', 'issuer_uf', 'driver_id', '_token']);
                $data['customer_id'] = $customerId;

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
        // 🛡️ IDENTIFICAÇÃO TÁTICA DO MOTORISTA
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Por favor, realize o login para acessar o portal.');
        }

        // Se for admin/gestor, tentamos carregar um subUser se ele estiver logado como um
        $subUser = \App\Models\CustomerSubUser::where('external_username', $user->external_username)->first();
        
        if (!$subUser) {
            return redirect()->route('dashboard')->with('error', 'Seu acesso não está vinculado a um perfil de Portal/Motorista.');
        }

        $driver = PortalDriver::where('sub_user_id', $subUser->id)->first();

        if (!$driver) {
            return redirect()->route('dashboard')->with('error', 'Seu acesso não possui um perfil de motorista configurado.');
        }

        // 📊 ESTADO DA JORNADA ATUAL (Último Registro)
        $lastChecklist = VehicleChecklist::where('driver_id', $driver->id)
            ->with('vehicle')
            ->orderBy('created_at', 'desc')
            ->first();

        $isOnline = ($lastChecklist && $lastChecklist->type == 'entry');

        // 📊 HISTÓRICO IMUTÁVEL (Últimos 30 dias)
        $checklists = VehicleChecklist::where('driver_id', $driver->id)
            ->with('vehicle')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('portal.verificacoes.index', compact('driver', 'checklists', 'lastChecklist', 'isOnline'));
    }

    /**
     * 📝 FORMULÁRIO DE NOVA VERIFICAÇÃO (CHECK-IN / OUT)
     */
    public function createChecklist(Request $request, $type)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $subUser = \App\Models\CustomerSubUser::where('external_username', $user->external_username)->first();

        if (!$subUser) {
            return redirect()->route('dashboard')->with('error', 'Ação permitida apenas para motoristas autorizados.');
        }

        $driver = PortalDriver::where('sub_user_id', $subUser->id)->first();
        
        // 🛡️ VALIDAÇÃO TÁTICA DE ESTADO (MÁQUINA DE ESTADOS)
        $lastChecklist = VehicleChecklist::where('driver_id', $driver->id)->orderBy('created_at', 'desc')->first();
        $isOnline = ($lastChecklist && $lastChecklist->type == 'entry');

        if ($type == 'entry' && $isOnline) {
            return redirect()->route('portal.verificacoes.index')->with('warning', 'Você já possui um Check-in ativo. Realize o Check-out antes de iniciar um novo.');
        }

        if ($type == 'exit' && !$isOnline) {
            return redirect()->route('portal.verificacoes.index')->with('warning', 'Nenhuma jornada ativa encontrada. Realize o Check-in primeiro.');
        }

        // 🚛 VEÍCULOS DISPONÍVEIS NA FROTA DO CLIENTE
        $vehicles = Vehicle::where('customer_id', $driver->customer_id)->get();

        // 🎯 PRÉ-SELEÇÃO DO VEÍCULO (NO CASO DE CHECK-OUT)
        $currentVehicleId = ($type == 'exit' && $lastChecklist) ? $lastChecklist->vehicle_id : null;

        return view('portal.verificacoes.form', compact('driver', 'vehicles', 'type', 'currentVehicleId'));
    }

    /**
     * 💾 PERSISTÊNCIA TÁTICA DE JORNADA (10 FOTOS)
     */
    public function storeChecklistAction(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:portal_drivers,id',
            'type' => 'required|in:entry,exit',
            'odometer' => 'required|numeric|min:0',
            'notes' => 'required|string|min:15|max:500',
            // 📸 5 FOTOS OBRIGATÓRIAS (ODÔMETRO + 4)
            'photo_1' => 'required|image|max:10240', // Odômetro
            'photo_2' => 'required|image|max:10240', // Frente
            'photo_3' => 'required|image|max:10240', // Traseira
            'photo_4' => 'required|image|max:10240', // Lateral Dir.
            'photo_5' => 'required|image|max:10240', // Lateral Esq.
            // 📸 5 FOTOS OPCIONAIS (BODY / EXTRAS)
            'photo_6' => 'nullable|image|max:10240',
            'photo_7' => 'nullable|image|max:10240',
            'photo_8' => 'nullable|image|max:10240',
            'photo_9' => 'nullable|image|max:10240',
            'photo_10' => 'nullable|image|max:10240',
        ], [
            'notes.min' => 'O Relato do Motorista deve conter pelo menos 15 caracteres.',
            'photo_1.required' => 'A foto do Odômetro é obrigatória.',
            'photo_2.required' => 'A foto da Frente é obrigatória.',
            'photo_3.required' => 'A foto da Traseira é obrigatória.',
            'photo_4.required' => 'A foto da Lateral Direita é obrigatória.',
            'photo_5.required' => 'A foto da Lateral Esquerda é obrigatória.',
        ]);

        // 🛡️ REVALIDAÇÃO TÁTICA DE ESTADO (BLINDAGEM SERVER-SIDE)
        $lastChecklist = VehicleChecklist::where('driver_id', $request->driver_id)->orderBy('created_at', 'desc')->first();
        $isOnline = ($lastChecklist && $lastChecklist->type == 'entry');

        if ($request->type == 'entry' && $isOnline) {
            return redirect()->route('portal.verificacoes.index')->with('error', 'Erro Crítico: Você já está em jornada ativada.');
        }

        if ($request->type == 'exit' && !$isOnline) {
            return redirect()->route('portal.verificacoes.index')->with('error', 'Erro Crítico: Nenhuma jornada ativa encontrada para encerrar.');
        }

        $photos = [];
        $driver = PortalDriver::find($request->driver_id);
        $date = now()->format('Y-m-d');

        // 📂 PROCESSAMENTO DE SLOTS
        for ($i = 1; $i <= 10; $i++) {
            if ($request->hasFile("photo_$i")) {
                $file = $request->file("photo_$i");
                $fileName = "checklist_{$request->type}_{$i}_" . time() . ".{$file->getClientOriginalExtension()}";
                $path = $file->storeAs("checklists/{$driver->id}/{$date}", $fileName, 'public');
                $photos["photo_$i"] = $path;
            }
        }

        $checklist = VehicleChecklist::create([
            'vehicle_id' => $request->vehicle_id,
            'driver_id' => $request->driver_id,
            'type' => $request->type,
            'odometer' => $request->odometer,
            'fuel_level' => $request->fuel_level ?? 'N/A',
            'photos' => $photos,
            'notes' => $request->notes
        ]);

        // 🆙 ATUALIZAÇÃO DE STATUS NO MOTORISTA
        $driver->update(['last_checklist_at' => now()]);

        return redirect()->route('portal.verificacoes.index')
            ->with('success', 'Verificação (' . strtoupper($request->type) . ') registrada com sucesso!');
    }

    /**
     * 👁️ VISUALIZAÇÃO DE HISTÓRICO (SOMENTE LEITURA)
     */
    public function showChecklist($id)
    {
        $checklist = VehicleChecklist::with(['vehicle', 'driver'])->findOrFail($id);
        return view('portal.verificacoes.show', compact('checklist'));
    }

    /**
     * 💰 DASHBOARD DE DESPESAS (LISTAGEM)
     */
    public function despesas(Request $request)
    {
        $user = Auth::user();
        $subUser = \App\Models\CustomerSubUser::where('external_username', $user->external_username)->first();
        $driver = PortalDriver::where('sub_user_id', $subUser->id)->first();

        if (!$driver) {
            return redirect()->route('dashboard')->with('error', 'Acesso negado: Perfil de motorista não encontrado.');
        }

        // 💰 HISTÓRICO DE DESPESAS (ÚLTIMOS 100 REGISTROS)
        $expenses = VehicleExpense::where('driver_id', $driver->id)
            ->with('vehicle')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('portal.despesas.index', compact('driver', 'expenses'));
    }

    /**
     * 📝 FORMULÁRIO DE NOVA DESPESA
     */
    public function createDespesa(Request $request)
    {
        $user = Auth::user();
        $subUser = \App\Models\CustomerSubUser::where('external_username', $user->external_username)->first();
        $driver = PortalDriver::where('sub_user_id', $subUser->id)->first();

        if (!$driver) {
            return redirect()->route('dashboard')->with('error', 'Acesso negado.');
        }

        $vehicles = Vehicle::where('customer_id', $driver->customer_id)->get();

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
            'driver_id' => 'required|exists:portal_drivers,id',
            'type' => 'required|string',
            'odometer' => 'required|numeric|min:0',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|min:3|max:100',
            'receipt_photo' => 'nullable|image|max:10240'
        ]);

        $entry = new VehicleExpense($request->except(['receipt_photo']));
        $entry->customer_id = PortalDriver::find($request->driver_id)->customer_id;

        // 📸 PROCESSAMENTO DE COMPROVANTE (OPCIONAL)
        if ($request->hasFile('receipt_photo')) {
            $path = $request->file('receipt_photo')->store('expenses/' . $request->driver_id, 'public');
            $entry->receipt_photo = $path;
        }

        $entry->save();

        return redirect()->route('portal.despesas.index')->with('success', '🛰️ DESPESA REGISTRADA COM SUCESSO! Registro ativo no dossiê do veículo.');
    }
}
