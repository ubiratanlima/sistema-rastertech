<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Installation;
use Illuminate\Support\Facades\Auth;

class AdminInstallationController extends Controller
{
    /**
     * 🚥 FILA DE HOMOLOGAÇÃO (VISÃO ADMIN/ATENDENTE)
     */
    public function index(Request $request)
    {
        $query = Installation::with(['installer', 'validator'])->orderBy('created_at', 'desc');

        // Filtros Táticos
        if ($request->has('status') && $request->status != '') {
            $query->where('validation_status', $request->status);
        }

        if ($request->has('search')) {
            $query->where('vehicle_plate', 'like', "%{$request->search}%")
                  ->orWhere('customer_name', 'like', "%{$request->search}%");
        }

        $installations = $query->paginate(20);
        return view('admin.installations.index', compact('installations'));
    }

    /**
     * 👁️ VISUALIZAÇÃO E TERMINAL DE TESTES
     */
    public function show($id)
    {
        $inst = Installation::with(['installer', 'validator'])->findOrFail($id);
        return view('admin.installations.show', compact('inst'));
    }

    /**
     * 🏁 REGISTRAR VALIDAÇÃO (AUDITORIA DE SINAL)
     */
    public function storeValidation(Request $request, $id)
    {
        $inst = Installation::findOrFail($id);

        // 🛡️ TRAVA DE SEGURANÇA: Impede re-validação de obra já aprovada
        if ($inst->validation_status == 'approved') {
            return redirect()->back()->with('error', 'Este dispositivo já foi homologado e não pode mais ser alterado.');
        }

        $request->validate([
            'validation_status' => 'required|in:approved,rejected',
            'validation_notes' => 'nullable|string|max:1000'
        ]);

        $inst->update([
            'test_online' => $request->has('test_online'),
            'test_block' => $request->has('test_block'),
            'test_ignition_on' => $request->has('test_ignition_on'),
            'test_ignition_off' => $request->has('test_ignition_off'),
            'validator_id' => Auth::id(),
            'validated_at' => now(),
            'validation_notes' => $request->validation_notes,
            'validation_status' => $request->validation_status
        ]);

        $statusEmoji = $request->validation_status == 'approved' ? '✅' : '❌';
        return redirect()->route('admin.installations.index')->with('success', "🛰️ VISTORIA #{$inst->id} ATUALIZADA COM SUCESSO {$statusEmoji}!");
    }
}
