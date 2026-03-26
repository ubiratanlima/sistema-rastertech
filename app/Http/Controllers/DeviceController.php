<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * Listagem Massiva de Rastreadores (Inventário)
     */
    public function index()
    {
        // Eager-loading mestre (Para carregar todos os nomes sem dar 1.000 queries)
        $devices = Device::with(['customer', 'gsmCard', 'deviceModel', 'platform', 'vehicle'])
                         ->latest()
                         ->paginate(20);

        return view('devices.index', compact('devices'));
    }

    /**
     * Detalhes do Rastreador (Onde aparece os Comandos SMS)
     */
    public function show($id)
    {
        $device = Device::with(['deviceModel.commands', 'gsmCard', 'platform', 'customer', 'vehicle'])
                        ->findOrFail($id);

        return view('devices.show', compact('device'));
    }
}
