<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PortalDriver;
use App\Models\VehicleChecklist;
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
     * REGISTRO DE CHECKLIST (ENTRADA/SAÍDA)
     */
    public function storeChecklist(Request $request)
    {
        // ... (Em desenvolvimento)
        return response()->json(['success' => 'Checklist registrado com sucesso!']);
    }
}
