<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeviceCommand;
use App\Models\DeviceModel;

class DeviceCommandController extends Controller
{
    /**
     * Lista todos os comandos SMS configurados por modelo.
     */
    public function index(Request $request)
    {
        $view      = $request->get('view', 'active');
        $sort      = $request->get('sort', 'description');
        $direction = $request->get('direction', 'asc');
        $search    = $request->get('search');

        $query = DeviceCommand::with('deviceModel');

        // 🌓 FILTRO: VISÃO TÁTICA (ATIVOS OU LIXEIRA)
        if ($view === 'trash') {
            $query->onlyTrashed();
        }

        // 🔍 FILTRO: BUSCA INTELIGENTE (CASE-INSENSITIVE)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('description', 'ILIKE', "%{$search}%")
                  ->orWhere('command_template', 'ILIKE', "%{$search}%")
                  ->orWhereHas('deviceModel', function($m) use ($search) {
                      $m->where('name', 'ILIKE', "%{$search}%");
                  });
            });
        }

        // 📶 ORDENAÇÃO DINÂMICA PADRONIZADA
        $commands = $query->orderBy($sort, $direction)
                          ->paginate(15)
                          ->withQueryString();
        
        $deviceModels = DeviceModel::orderBy('name')->get();

        // Preparação de dados JSON para os Dossiês/Edição
        $commandData = $commands->mapWithKeys(function ($c) {
            return [$c->id => [
                'id'               => $c->id,
                'device_model_id'  => $c->device_model_id,
                'model_name'       => $c->deviceModel->name,
                'description'      => $c->description,
                'command_template' => $c->command_template,
                'execution_order'  => $c->execution_order,
                'deleted_at'       => $c->deleted_at
            ]];
        });
        
        return view('device-commands.index', compact('commands', 'deviceModels', 'view', 'sort', 'direction', 'search', 'commandData'));
    }

    /**
     * Cadastra um novo Template de Comando SMS.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'device_model_id' => 'required|exists:device_models,id',
            'description' => 'required|max:100',
            'command_template' => 'required',
            'execution_order' => 'required|integer'
        ]);

        DeviceCommand::create($validated);

        return redirect()->back()->with('success', '💬 Template de Comando registrado com sucesso!');
    }

    /**
     * Atualiza um template de comando (Suporte AJAX).
     */
    public function update(Request $request, $id)
    {
        $command = DeviceCommand::findOrFail($id);
        
        $validated = $request->validate([
            'description'      => 'required|max:100',
            'command_template' => 'required',
            'execution_order'  => 'required|integer'
        ]);

        $command->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Template de comando atualizado na biblioteca.'
        ]);
    }

    /**
     * Restaura um comando inativado.
     */
    public function restore($id)
    {
        $command = DeviceCommand::withTrashed()->findOrFail($id);
        $command->restore();

        return redirect()
            ->route('device-commands.index')
            ->with('success', 'Comando reativado com sucesso');
    }

    /**
     * Remove um template de comando (SoftDelete).
     */
    public function destroy($id)
    {
        try {
            $command = DeviceCommand::findOrFail($id);
            $command->delete();

            if (request()->ajax()) {
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Comando desativado com sucesso.'
                ]);
            }

            return redirect()
                ->route('device-commands.index')
                ->with('success', 'Comando desativado com sucesso.');

        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Erro interno ao inativar comando: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }
}
