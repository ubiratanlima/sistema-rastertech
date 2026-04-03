<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DeviceModel;

class DeviceModelController extends Controller
{
    /**
     * Lista todos os modelos de hardware com filtros RTECH.
     */
    public function index(Request $request)
    {
        $view      = $request->get('view', 'active');
        $sort      = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');
        $search    = $request->get('search');

        $query = DeviceModel::withCount('devices');

        // 🌓 FILTRO: VISÃO TÁTICA (ATIVOS OU LIXEIRA)
        if ($view === 'trash') {
            $query->onlyTrashed();
        }

        // 🔍 FILTRO: BUSCA INTELIGENTE (CASE-INSENSITIVE)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('manufacturer', 'ILIKE', "%{$search}%");
            });
        }

        // 📶 ORDENAÇÃO DINÂMICA PADRONIZADA
        $models = $query->orderBy($sort, $direction)
                        ->paginate(15)
                        ->withQueryString();

        // Preparação de dados JSON para os Dossiês/Edição
        $modelData = $models->mapWithKeys(function ($m) {
            return [$m->id => [
                'id'           => $m->id,
                'name'         => $m->name,
                'manufacturer' => $m->manufacturer,
                'devices'      => $m->devices_count,
                'deleted_at'   => $m->deleted_at
            ]];
        });

        return view('device-models.index', compact('models', 'modelData', 'view', 'sort', 'direction', 'search'));
    }

    /**
     * Cadastra um novo modelo técnico.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'manufacturer' => 'nullable|max:100'
        ]);

        DeviceModel::create($validated);

        return redirect()->back()->with('success', '🛰️ Inteligência de Hardware: Modelo cadastrado com sucesso!');
    }

    /**
     * Edição Tática de Modelos via AJAX.
     */
    public function update(Request $request, $id)
    {
        $model     = DeviceModel::findOrFail($id);
        $validated = $request->validate([
            'name'         => 'required|max:100',
            'manufacturer' => 'nullable|max:100'
        ]);

        $model->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Ficha técnica sincronizada.']);
        }

        return redirect()->back()->with('success', 'Ficha atualizada com sucesso!');
    }

    /**
     * Inativar modelo com trava de segurança.
     */
    public function destroy($id)
    {
        $model = DeviceModel::withCount('devices')->findOrFail($id);

        if ($model->devices_count > 0) {
            return redirect()->back()->with('error', 'Bloqueio de Engenharia! Existem aparelhos vinculados a este modelo. Remova-os do estoque para inativar.');
        }

        $model->delete();

        return redirect()->back()->with('success', 'Modelo técnico movido para a lixeira.');
    }

    /**
     * Reativar modelo no radar operacional.
     */
    public function restore($id)
    {
        $model = DeviceModel::withTrashed()->findOrFail($id);
        $model->restore();

        return redirect()->back()->with('success', 'Modelo técnico reativado no radar!');
    }
}
