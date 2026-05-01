<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Installation;
use Illuminate\Support\Facades\Auth;

class InstallerPortalController extends Controller
{
    /**
     * 📊 DASHBOARD DO INSTALADOR (ESTADOS TÁTICOS)
     */
    public function index()
    {
        $user = auth()->user();
        $query = Installation::with('installer');

        $adminRoles = ['admin', 'gerente', 'suporte', 'administrador'];
        $userRole = strtolower($user->role);
        
        if (!in_array($userRole, $adminRoles)) {
            // Se for apenas instalador, vê apenas as suas próprias obras
            $query->where('installer_id', $user->id);
        }

        $installations = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('portal.instalador.index', compact('installations'));
    }

    /* 
     ==========================================================================
     Fase 1: CHECK-IN (VISTORIA DE ENTRADA)
     ==========================================================================
    */
    public function createCheckin()
    {
        return view('portal.instalador.form_checkin');
    }

    public function storeCheckin(Request $request)
    {
        $request->validate([
            'vehicle_plate' => 'required|string|max:10',
            'customer_name' => 'required|string|max:150',
            'vehicle_details' => 'nullable|string',
            'frente' => 'required|image|max:10240',
            'placa_frente' => 'required|image|max:10240',
            'lat_dir' => 'required|image|max:10240',
            'lat_esq' => 'required|image|max:10240',
            'traseira' => 'required|image|max:10240',
            'odometro' => 'required|image|max:10240',
            'interna_pre' => 'required|image|max:10240',
        ]);

        $photos = [];
        $mandatory = ['frente', 'placa_frente', 'lat_dir', 'lat_esq', 'traseira', 'odometro', 'interna_pre'];
        $extras = ['extra_1', 'extra_2', 'extra_3', 'extra_4'];

        foreach (array_merge($mandatory, $extras) as $slot) {
            if ($request->hasFile($slot)) {
                $path = $request->file($slot)->store("installations/checkin/" . Auth::id() . "/" . date('Y-m-d'), 'public');
                $photos[$slot] = $path;
            }
        }

        Installation::create([
            'installer_id' => Auth::id(),
            'customer_name' => $request->customer_name,
            'vehicle_plate' => strtoupper($request->vehicle_plate),
            'vehicle_details' => $request->vehicle_details,
            'status' => 'checked_in',
            'checkin_at' => now(),
            'checkin_photos' => $photos,
        ]);

        return redirect()->route('portal.instalador.index')->with('success', '🛰️ CHECK-IN CONCLUÍDO! INICIE A INSTALAÇÃO ELÉTRICA.');
    }

    /* 
     ==========================================================================
     Fase 2: PROCESSO ELÉTRICO (DURING INSTALLATION)
     ==========================================================================
    */
    public function createProcess($id)
    {
        $installation = Installation::where('installer_id', Auth::id())->where('status', 'checked_in')->findOrFail($id);
        return view('portal.instalador.form_process', compact('installation'));
    }

    public function storeProcess(Request $request, $id)
    {
        $installation = Installation::where('installer_id', Auth::id())->findOrFail($id);
        
        $rules = [
            'chicote' => 'required|image|max:10240',
            'acc' => 'required|image|max:10240',
            'positivo' => 'required|image|max:10240',
            'neutro' => 'required|image|max:10240',
        ];

        // 🚥 VALIDAÇÃO CONDICIONAL DE BLOQUEIO
        if ($request->has('has_block')) {
            $rules['bloqueio'] = 'required|image|max:10240';
            $rules['rele'] = 'required|image|max:10240';
        }

        $request->validate($rules);

        $photos = [];
        $slots = ['chicote', 'bloqueio', 'acc', 'positivo', 'neutro', 'rele'];
        foreach ($slots as $slot) {
            if ($request->hasFile($slot)) {
                $path = $request->file($slot)->store("installations/process/" . Auth::id() . "/" . date('Y-m-d'), 'public');
                $photos[$slot] = $path;
            }
        }

        $installation->update([
            'status' => 'processing',
            'has_block' => $request->has('has_block'),
            'processed_at' => now(),
            'process_photos' => $photos
        ]);

        return redirect()->route('portal.instalador.index')->with('success', '⚡ PARTE ELÉTRICA REGISTRADA! AGUARDANDO CHECKOUT.');
    }

    /* 
     ==========================================================================
     Fase 3: CHECK-OUT (ENTREGA FINAL)
     ==========================================================================
    */
    public function createCheckout($id)
    {
        $installation = Installation::where('installer_id', Auth::id())->where('status', 'processing')->findOrFail($id);
        return view('portal.instalador.form_checkout', compact('installation'));
    }

    public function storeCheckout(Request $request, $id)
    {
        $installation = Installation::where('installer_id', Auth::id())->findOrFail($id);
        
        $request->validate([
            'interna_pos' => 'required|image|max:10240',
            'acompanhante' => 'required|image|max:10240',
            'documento_cliente' => 'required|image|max:10240',
            'notes' => 'required|string|max:500'
        ]);

        $photos = [];
        $slots = ['interna_pos', 'acompanhante', 'documento_cliente'];
        foreach ($slots as $slot) {
            if ($request->hasFile($slot)) {
                $path = $request->file($slot)->store("installations/checkout/" . Auth::id() . "/" . date('Y-m-d'), 'public');
                $photos[$slot] = $path;
            }
        }

        $installation->update([
            'status' => 'completed',
            'completed_at' => now(),
            'checkout_photos' => $photos,
            'checkout_notes' => $request->notes,
            'customer_id_photo' => $photos['documento_cliente'] // Para compatibilidade
        ]);

        return redirect()->route('portal.instalador.index')->with('success', '🏁 INSTALAÇÃO FINALIZADA E BLINDADA COM SUCESSO!');
    }

    /**
     * 👁️ VISUALIZAÇÃO DE DOSSIÊ COMPLETO
     */
    public function show($id)
    {
        $user = auth()->user();
        $adminRoles = ['admin', 'gerente', 'suporte', 'administrador'];
        $userRole = strtolower($user->role);
        
        $query = Installation::query();
        if (!in_array($userRole, $adminRoles)) {
            $query->where('installer_id', $user->id);
        }

        $inst = $query->findOrFail($id);
        
        // Busca o histórico de atendimentos vinculado à placa do veículo
        $attendances = \App\Models\Attendance::whereHas('vehicle', function($q) use ($inst) {
                $q->where('plate', $inst->vehicle_plate);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('portal.instalador.show', compact('inst', 'attendances'));
    }
}
