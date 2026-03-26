<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Platform;

class PlatformController extends Controller
{
    /**
     * Lista todos os Servidores / Sistemas de Operação.
     */
    public function index()
    {
        // 🧬 Buscando plataformas com contagem de rastreadores apontados para elas
        $platforms = Platform::withCount('devices')->paginate(15);
        
        return view('platforms.index', compact('platforms'));
    }

    /**
     * Cadastra um novo Servidor ou Sistema.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'url' => 'nullable|url|max:255',
            'server_ip' => 'required|max:45',
            'supplier_name' => 'nullable|max:100'
        ]);

        Platform::create($validated);

        return redirect()->back()->with('success', '🛰️ Plataforma de Operação cadastrada no radar!');
    }

    /**
     * Inativa uma Plataforma com trava de segurança operacional.
     */
    public function destroy($id)
    {
        $platform = Platform::withCount('devices')->findOrFail($id);

        // 🔒 VERIFICAÇÃO DE SEGURANÇA: Existem dispositivos enviando dados para este IP?
        if ($platform->devices_count > 0) {
            return redirect()
                ->route('platforms.index')
                ->with('error', 'Bloqueio de Infraestrutura! Existem rastreadores enviando dados para este servidor. Redirecione os aparelhos antes de remover.');
        }

        $platform->delete();

        return redirect()
            ->route('platforms.index')
            ->with('success', 'Servidor removido da base técnica com sucesso!');
    }
}
