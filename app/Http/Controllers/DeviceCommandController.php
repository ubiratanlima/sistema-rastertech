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
    public function index()
    {
        // 🧬 Buscando comandos com o modelo vinculado, ordenados por execução
        $commands = DeviceCommand::with('deviceModel')
            ->orderBy('device_model_id')
            ->orderBy('execution_order')
            ->paginate(15);
        
        $deviceModels = DeviceModel::orderBy('name')->get();
        
        return view('device-commands.index', compact('commands', 'deviceModels'));
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
     * Remove um template de comando.
     */
    public function destroy($id)
    {
        $command = DeviceCommand::findOrFail($id);
        $command->delete();

        return redirect()
            ->route('device-commands.index')
            ->with('success', 'Template de comando removido da biblioteca.');
    }
}
