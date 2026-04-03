<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Platform;

class PlatformController extends Controller
{
    /**
     * Lista todos os Servidores / Sistemas de Operação com filtros Padrão Ouro.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $view = $request->input('view', 'active');
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');

        // 🧬 Query Base
        $query = Platform::withCount('devices');

        // 🔍 Filtro de Busca (Nome, IP ou Fornecedor)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('server_ip', 'ILIKE', "%{$search}%")
                  ->orWhere('supplier_name', 'ILIKE', "%{$search}%");
            });
        }

        // ⚙️ Seletor de Visão (Ativos vs Lixeira/Inativos)
        if ($view == 'trash') {
            $query->onlyTrashed();
        }

        // ↕️ Ordenação Dinâmica
        $query->orderBy($sort, $direction);

        $platforms = $query->paginate(15)->appends($request->all());
        
        return view('platforms.index', compact('platforms', 'search', 'view', 'sort', 'direction'));
    }

    /**
     * Cadastra um novo Servidor ou Sistema.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'server_ip' => 'required|max:45',
            'url' => 'nullable|url',
            'supplier_name' => 'nullable|max:100',
            'app_android_url' => 'nullable|url',
            'app_ios_url' => 'nullable|url',
        ]);

        Platform::create($validated);

        return redirect()->back()->with('success', '🛰️ Plataforma de Operação cadastrada no radar!');
    }

    /**
     * Atualiza os dados de uma Plataforma.
     */
    public function update(Request $request, $id)
    {
        $platform = Platform::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|max:100',
            'server_ip' => 'required|max:45',
            'url' => 'nullable|url',
            'supplier_name' => 'nullable|max:100',
            'app_android_url' => 'nullable|url',
            'app_ios_url' => 'nullable|url',
        ]);

        $platform->update($validated);

        return redirect()->route('platforms.index')->with('success', '🛰️ Plataforma atualizada com sucesso no radar!');
    }

    /**
     * Inativa uma Plataforma com trava de segurança operacional.
     */
    public function destroy($id)
    {
        $platform = Platform::withCount('devices')->findOrFail($id);

        // 🔒 VERIFICAÇÃO DE SEGURANÇA
        if ($platform->devices_count > 0) {
            return redirect()
                ->route('platforms.index')
                ->with('error', 'Bloqueio de Infraestrutura! Existem rastreadores enviando dados para este servidor. Redirecione os aparelhos antes de remover.');
        }

        $platform->delete();

        return redirect()
            ->route('platforms.index')
            ->with('success', 'Servidor movido para a base de inativos com sucesso!');
    }

    /**
     * Restaura uma plataforma inativada.
     */
    public function restore($id)
    {
        $platform = Platform::onlyTrashed()->findOrFail($id);
        $platform->restore();

        return redirect()
            ->route('platforms.index')
            ->with('success', 'Plataforma reativada com sucesso na infraestrutura!');
    }
}
