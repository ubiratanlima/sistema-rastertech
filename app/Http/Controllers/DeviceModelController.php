<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DeviceModel;

class DeviceModelController extends Controller
{
    /**
     * Lista todos os modelos de hardware e seus fabricantes.
     */
    public function index()
    {
        // 🧬 Buscando modelos com contagem de aparelhos físicos vinculados
        $models = DeviceModel::withCount('devices')->paginate(15);
        
        return view('device-models.index', compact('models'));
    }

    /**
     * Cadastra um novo modelo técnico de rastreador.
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
     * Inativa um modelo de hardware com trava de segurança de estoque.
     */
    public function destroy($id)
    {
        $model = DeviceModel::withCount('devices')->findOrFail($id);

        // 🔒 VERIFICAÇÃO DE SEGURANÇA: Existem aparelhos deste modelo no inventário?
        if ($model->devices_count > 0) {
            return redirect()
                ->route('device-models.index')
                ->with('error', 'Bloqueio de Engenharia! Existem aparelhos vinculados a este modelo no inventário. Remova os rastreadores antes de inativar o modelo.');
        }

        $model->delete();

        return redirect()
            ->route('device-models.index')
            ->with('success', 'Modelo técnico removido da base de dados com sucesso!');
    }
}
