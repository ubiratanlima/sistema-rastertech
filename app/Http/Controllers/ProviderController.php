<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Provider;

class ProviderController extends Controller
{
    /**
     * Lista todos os Fornecedores com inteligência de inventário e filtros Padrão Ouro.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $view = $request->input('view', 'active');
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');

        // 🧬 Query Base
        $query = Provider::withCount(['devices', 'gsmCards']);

        // 🔍 Filtro de Busca (Nome, Documento ou E-mail)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%")
                  ->orWhere('document', 'ILIKE', "%{$search}%");
            });
        }

        // ⚙️ Seletor de Visão (Ativos vs Lixeira/Inativos)
        if ($view == 'trash') {
            $query->onlyTrashed();
        }

        // ↕️ Ordenação Dinâmica
        $query->orderBy($sort, $direction);

        $providers = $query->paginate(15)->appends($request->all());
        
        return view('providers.index', compact('providers', 'search', 'view', 'sort', 'direction'));
    }

    /**
     * Cadastra um novo Fornecedor / Parceiro.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'         => 'required|max:100',
                'type'         => 'required|in:hardware,connectivity,software',
                'email'        => 'nullable|email|max:150',
                'phone'        => 'nullable|max:20',
                'document'     => 'nullable|max:20',
                'contact_name' => 'nullable|max:100',
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
     * Atualiza os dados de um Fornecedor.
     */
    public function update(Request $request, $id)
    {
        $provider = Provider::findOrFail($id);

        $validated = $request->validate([
            'name'         => 'required|max:100',
            'type'         => 'required|in:hardware,connectivity,software',
            'email'        => 'nullable|email|max:150',
            'phone'        => 'nullable|max:20',
            'document'     => 'nullable|max:20',
            'contact_name' => 'nullable|max:100',
        ]);

        $provider->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Fornecedor atualizado com sucesso!']);
        }

        return redirect()->route('providers.index')->with('success', 'Fornecedor atualizado com sucesso!');
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
    /**
     * Restaura um fornecedor inativado.
     */
    public function restore($id)
    {
        $provider = Provider::onlyTrashed()->findOrFail($id);
        $provider->restore();

        return redirect()
            ->route('providers.index')
            ->with('success', 'Fornecedor reativado com sucesso na base estratégica!');
    }
}
