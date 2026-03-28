<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Provider;

class ProviderController extends Controller
{
    /**
     * Lista todos os Fornecedores com inteligência de inventário.
     */
    public function index()
    {
        // 🧬 Buscando fornecedores com contagem de equipamentos e chips vinculados
        $providers = Provider::withCount(['devices', 'gsmCards'])->paginate(15);
        
        return view('providers.index', compact('providers'));
    }

    /**
     * Cadastra um novo Fornecedor / Parceiro.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|max:100',
                'type' => 'required|in:hardware,connectivity,software'
            ]);

            $provider = Provider::create($validated);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => '🏢 Fornecedor registrado com sucesso!',
                    'provider' => $provider
                ]);
            }

            return redirect()->back()->with('success', '🏢 Fornecedor cadastrado com sucesso na base de Engenharia!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            throw $e;
        }
    }

    /**
     * Inativa um Fornecedor com trava de segurança de estoque.
     */
    public function destroy($id)
    {
        $provider = Provider::withCount(['devices', 'gsmCards'])->findOrFail($id);

        // 🔒 VERIFICAÇÃO DE SEGURANÇA: Existem ativos deste fornecedor no sistema?
        if ($provider->devices_count > 0 || $provider->gsm_cards_count > 0) {
            return redirect()
                ->route('providers.index')
                ->with('error', 'Bloqueio de Segurança! Existem chips ou equipamentos vinculados a este fornecedor. Limpe o estoque antes de remover.');
        }

        $provider->delete();

        return redirect()
            ->route('providers.index')
            ->with('success', 'Fornecedor removido da base de dados com sucesso!');
    }
}
