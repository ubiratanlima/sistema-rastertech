<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DeviceCommand;
use App\Models\DeviceModel;

class DeviceCommandController extends Controller
{
    /**
     * Lista todos os comandos SMS agrupados por modelo para visualização em acordeon.
     */
    public function index(Request $request)
    {
        $view   = $request->get('view', 'active');
        $search = $request->get('search');

        $query = DeviceCommand::has('deviceModel')->with('deviceModel');

        if ($view === 'trash') {
            $query->onlyTrashed();
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('description', 'ILIKE', "%{$search}%")
                  ->orWhere('command_template', 'ILIKE', "%{$search}%")
                  ->orWhereHas('deviceModel', function($m) use ($search) {
                      $m->where('name', 'ILIKE', "%{$search}%");
                  });
            });
        }

        // 🧬 Agrupamento estratégico por Modelo
        $commandsGrouped = $query->orderBy('execution_order', 'asc')
                                 ->get()
                                 ->groupBy('device_model_id');
        
        $deviceModels = DeviceModel::orderBy('name')->get();
        
        return view('device-commands.index', compact('commandsGrouped', 'deviceModels', 'view', 'search'));
    }

    /**
     * Salva uma lista de comandos em lote para um modelo.
     */
    public function batchStore(Request $request)
    {
        $validated = $request->validate([
            'device_model_id' => 'required|exists:device_models,id',
            'commands'         => 'required|array|min:1',
            'commands.*.description' => 'required|max:100',
            'commands.*.command_template' => 'required',
            'commands.*.execution_order'  => 'required|integer'
        ]);

        foreach ($validated['commands'] as $cmd) {
            DeviceCommand::create([
                'device_model_id'  => $validated['device_model_id'],
                'description'      => $cmd['description'],
                'command_template' => $cmd['command_template'],
                'execution_order'  => $cmd['execution_order']
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Biblioteca de comandos sincronizada com sucesso!']);
    }

    /**
     * Retorna todos os comandos de um modelo para edição.
     */
    public function getCommandsByModel($modelId)
    {
        $commands = DeviceCommand::where('device_model_id', $modelId)
                                 ->orderBy('execution_order', 'asc')
                                 ->get();
        
        return response()->json($commands);
    }

    /**
     * Sincroniza (Atualiza/Remove/Cria) comandos de um modelo em lote.
     */
    public function batchUpdate(Request $request, $modelId)
    {
        $validated = $request->validate([
            'commands' => 'required|array',
            'commands.*.id' => 'nullable|exists:device_commands,id',
            'commands.*.description' => 'required|max:100',
            'commands.*.command_template' => 'required',
            'commands.*.execution_order'  => 'required|integer',
            'removed_ids' => 'nullable|array',
            'removed_ids.*' => 'exists:device_commands,id'
        ]);

        DB::transaction(function() use ($validated, $modelId) {
            // 1. Remover excluídos
            if (!empty($validated['removed_ids'])) {
                DeviceCommand::whereIn('id', $validated['removed_ids'])->delete();
            }

            // 2. Atualizar ou Criar
            foreach ($validated['commands'] as $cmd) {
                DeviceCommand::updateOrCreate(
                    ['id' => $cmd['id'] ?? null],
                    [
                        'device_model_id'  => $modelId,
                        'description'      => $cmd['description'],
                        'command_template' => $cmd['command_template'],
                        'execution_order'  => $cmd['execution_order']
                    ]
                );
            }
        });

        return response()->json(['success' => true, 'message' => 'Configurações de modelo atualizadas com sucesso!']);
    }

    /**
     * Restaura um comando inativado.
     */
    public function restore($id)
    {
        $command = DeviceCommand::withTrashed()->findOrFail($id);
        $command->restore();

        return redirect()->route('device-commands.index')->with('success', 'Comando reativado!');
    }

    /**
     * Remove um único comando (SoftDelete).
     */
    public function destroy($id)
    {
        $command = DeviceCommand::findOrFail($id);
        $command->delete();
        return response()->json(['success' => true]);
    }
}
