<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\Attendance;
use Illuminate\Support\Facades\Storage;

class SupportController extends Controller
{
    /**
     * Lista clientes ativos que possuem veículos vinculados (Nível Atendimento).
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Query Blindada: Busca apenas clientes únicos que possuem ao menos um veículo
        $query = DB::table('customers')
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from('vehicles')
                  ->whereColumn('vehicles.customer_id', 'customers.id');
            })
            ->select(
                'customers.id',
                'customers.name',
                'customers.document',
                'customers.code as customer_rtech',
                DB::raw("(SELECT COUNT(*) FROM vehicles WHERE customer_id = customers.id) as vehicle_count")
            );

        // Filtro tático rápido (Nome, CNPJ/CPF ou RTECH CODE)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('customers.name', 'ILIKE', "%{$search}%")
                  ->orWhere('customers.document', 'ILIKE', "%{$search}%")
                  ->orWhere('customers.code', 'ILIKE', "%{$search}%");
            });
        }

        $customers = $query->paginate(15);

        // Injeção de dados de veículos para o sistema de Acordeon
        foreach ($customers as $customer) {
            $customer->vehicles = DB::table('vehicles')
                ->leftJoin('devices', function($join) {
                    $join->on('vehicles.id', '=', 'devices.vehicle_id')
                         ->where('devices.status', '=', 'active');
                })
                ->leftJoin('gsm_cards', 'devices.gsm_card_id', '=', 'gsm_cards.id')
                ->where('vehicles.customer_id', $customer->id)
                ->select(
                    'vehicles.id',
                    'vehicles.plate',
                    'vehicles.brand',
                    'vehicles.model',
                    'devices.model_description as rtech_code',
                    'devices.imei',
                    'devices.status as device_status',
                    'gsm_cards.phone_number',
                    'gsm_cards.operator'
                )
                ->get();
            
            // Pega o código RTECH de um dos veículos para a linha de base (se houver)
            $customer->primary_rtech = $customer->vehicles->first()->rtech_code ?? '---';

            // 🎯 INJEÇÃO NÍVEL 3: Carrega históricos para cada veículo do cliente
            foreach ($customer->vehicles as $vehicle) {
                $vehicle->attendances = Attendance::with('user')
                    ->where('vehicle_id', $vehicle->id)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
            }
        }

        return view('support.index', compact('customers', 'search'));
    }

    /**
     * Inicia o atendimento (Gatilho para o Cockpit)
     */
    public function start($vehicleId, $customerId)
    {
        $vehicle = Vehicle::with(['customer', 'devices.gsmCard'])->findOrFail($vehicleId);
        $customer = Customer::findOrFail($customerId);

        // Busca histórico de atendimentos do veículo (Acordeon Nível 3)
        $history = Attendance::with('user')
            ->where('vehicle_id', $vehicleId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Automação Tática de Papel (Role Based Classification)
        $user = auth()->user();
        $defaultType = 'support';
        $allowTypeSelection = $user && in_array($user->role, ['admin', 'manager', 'Gerente', 'Administrador', 'Gestor de Operações']);

        if ($user && $user->role == 'instalador') {
            $defaultType = 'installation';
        }

        return view('support.start', compact('vehicle', 'customer', 'history', 'defaultType', 'allowTypeSelection'));
    }

    /**
     * Finaliza o atendimento (Salvamento Híbrido: SQL + TXT)
     */
    public function finish(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'type' => 'required',
            'history' => 'required'
        ]);

        // 1. Persistência em Banco (Metadados)
        $logPath = Attendance::generateLogPath($request->customer_id, $request->vehicle_id);
        
        $attendance = Attendance::create([
            'customer_id' => $request->customer_id,
            'vehicle_id' => $request->vehicle_id,
            'user_id' => auth()->id(),
            'type' => $request->type,
            'log_path' => $logPath
        ]);

        // 2. Geração do Dossiê Blindado (.txt)
        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $customer = Customer::findOrFail($request->customer_id);
        
        // Mapeamento de nomes para o dossiê físico
        $typeNames = [
            'support' => 'SUPORTE TÉCNICO',
            'installation' => 'INSTALAÇÃO',
            'administrative' => 'ADMINISTRATIVO',
            'commercial' => 'COMERCIAL'
        ];
        $displayType = $typeNames[$request->type] ?? strtoupper($request->type);

        $content = "RASTERTECH - DOSSIÊ DE ATENDIMENTO TÉCNICO\n";
        $content .= "==========================================\n";
        $content .= "ID ATENDIMENTO: " . $attendance->id . "\n";
        $content .= "DATA/HORA: " . now()->format('d/m/Y H:i:s') . "\n";
        $content .= "ATENDENTE: " . auth()->user()->name . "\n";
        $content .= "TIPO: " . $displayType . "\n\n";
        
        $content .= "DADOS DO CLIENTE\n";
        $content .= "---------------\n";
        $content .= "NOME: " . $customer->name . "\n";
        $content .= "DOC: " . $customer->document . "\n\n";
        
        $content .= "DADOS DO VEÍCULO\n";
        $content .= "----------------\n";
        $content .= "PLACA: " . $vehicle->plate . "\n";
        $content .= "MARCA/MODELO: " . $vehicle->brand . " / " . $vehicle->model . "\n\n";
        
        $content .= "RELATO TÉCNICO (HISTÓRICO)\n";
        $content .= "--------------------------\n";
        $content .= $request->history . "\n\n";
        $content .= "==========================================\n";
        $content .= "FIM DO REGISTRO\n";

        // Salva arquivo no storage (Padrão: atendimentos/{id_cli}/atendimento_{id_vei}_{data}.txt)
        Storage::disk('local')->put($logPath, $content);

        return redirect()->route('support.customers')->with('success', 'Atendimento finalizado e dossiê blindado com sucesso!');
    }

    /**
     * Exibe o arquivo log físico (.txt) do atendimento
     */
    public function viewLog(Attendance $attendance)
    {
        // Verifica se o arquivo existe no storage local
        if (!Storage::disk('local')->exists($attendance->log_path)) {
            return abort(404, 'Dossiê técnico não encontrado no servidor.');
        }

        $content = Storage::disk('local')->get($attendance->log_path);

        return response($content, 200)
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }
}
