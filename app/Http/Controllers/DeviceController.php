<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\GsmCard;
use App\Models\Customer;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeviceController extends Controller
{
    /**
     * GESTÃO DE HARDWARE (MODO OPERAÇÃO)
     * Sistema de inventário tático com suporte a quadri-estado.
     */
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'updated_at');
        $direction = $request->get('direction', 'desc');
        $search = $request->get('search');
        $view = $request->get('view', 'active'); // active, inventory, trash, canceled

        // 🧬 Engine de Consulta Universal
        $query = Device::withTrashed()
            ->with(['customer', 'deviceModel', 'gsmCard', 'vehicle'])
            ->select('devices.*')
            ->leftJoin('customers', 'devices.customer_id', '=', 'customers.id')
            ->leftJoin('gsm_cards', 'devices.gsm_card_id', '=', 'gsm_cards.id')
            ->leftJoin('device_models', 'devices.device_model_id', '=', 'device_models.id');

        // 🔍 Busca Multi-DADOS (IMEI, RTECH CODE ou Cliente)
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('customers.name', 'ILIKE', "%$search%")
                  ->orWhere('devices.imei', 'ILIKE', "%$search%")
                  ->orWhere('devices.internal_code', 'ILIKE', "%$search%");
            });
        }

        // ⚙️ Filtro de Visão
        switch ($view) {
            case 'inventory':
                $query->whereNull('devices.deleted_at')
                      ->whereNotIn('devices.status', ['active', 'canceled']);
                break;
            case 'canceled':
                $query->whereNull('devices.deleted_at')
                      ->where('devices.status', 'canceled');
                break;
            case 'trash':
                $query->whereNotNull('devices.deleted_at');
                break;
            case 'active':
            default:
                $view = 'active';
                $query->whereNull('devices.deleted_at')
                      ->where('devices.status', 'active');
                break;
        }

        // ⚖️ Ordenação
        $sortColumns = [
            'id' => 'devices.id',
            'imei' => 'devices.imei',
            'internal_code' => 'devices.internal_code',
            'status' => 'devices.status',
            'cliente' => 'customers.name',
            'modelo' => 'device_models.name',
            'chip' => 'gsm_cards.iccid'
        ];
        $orderBySelect = $sortColumns[$sort] ?? 'devices.updated_at';
        $query->orderBy($orderBySelect, $direction);

        $devices_list = $query->paginate(15)
                ->withPath('/devices')
                ->withQueryString();

        // Dados para os seletores dos modais
        $customers = Customer::orderBy('name')->get(['id', 'name']);
        $deviceModels = DeviceModel::orderBy('name')->get(['id', 'name']);
        $sims = GsmCard::whereDoesntHave('device')->orderBy('iccid')->get(['id', 'iccid']);
        $freeVehicles = \App\Models\Vehicle::whereDoesntHave('devices')->orderBy('plate')->get(['id', 'plate', 'customer_id']);
        $providers = Provider::where('type', 'hardware')->orderBy('name')->get(['id', 'name']);
        
        // 🔮 Sugestão de Próximo RTECH CODE
        $lastId = DB::table('devices')->max('id') ?? 0;
        $nextCode = 'RTECH-' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);

        return view('devices.index', compact('devices_list', 'sort', 'direction', 'search', 'view', 'customers', 'deviceModels', 'sims', 'freeVehicles', 'providers', 'nextCode'));
    }

    /**
     * ASSISTENTE DE REGISTRO TÁTICO (WIZARD SINGLE/MASS)
     */
    public function store(Request $request)
    {
        try {
            // 🧙‍♂️ MODO WIZARD (REGISTRO EM MASSA)
            if ($request->has('wizard')) {
                $equipments = json_decode($request->get('equipments'), true);
                $common = $request->only(['device_model_id', 'provider_id', 'customer_id']);
                
                // 🤖 Lógica de Status Automático: Com cliente = ATIVO, Sem cliente = ESTOQUE
                $common['status'] = empty($common['customer_id']) ? 'inactive' : 'active';

                DB::transaction(function() use ($equipments, $common) {
                    foreach ($equipments as $item) {
                        Device::create(array_merge($common, [
                            'imei' => $item['imei'],
                            'internal_code' => $item['internal_code'] ?? null,
                            'model_description' => $item['model'] ?? 'N/A'
                        ]));
                    }
                });

                return redirect('/devices')->with('success', count($equipments) . ' equipamentos registrados com sucesso!');
            }

            // 🛠️ MODO MANUAL (SINGLE)
            $validated = $request->validate([
                'imei' => 'required|unique:devices,imei',
                'internal_code' => 'nullable|unique:devices,internal_code',
                'device_model_id' => 'required|exists:device_models,id',
                'status' => 'nullable',
                'customer_id' => 'nullable|exists:customers,id'
            ]);

            if (empty($validated['status'])) {
                $validated['status'] = empty($validated['customer_id']) ? 'inactive' : 'active';
            }

            Device::create($validated);
            return redirect('/devices')->with('success', 'Equipamento registrado individualmente.');

        } catch (\Exception $e) {
            Log::error("❌ ERRO NO REGISTRO DE HARDWARE", ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Falha na persistência: ' . $e->getMessage());
        }
    }

    /**
     * SINCRONIZAÇÃO TÁTICA (UPDATE AJAX)
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'imei' => 'required|max:50|unique:devices,imei,' . $id,
                'internal_code' => 'required|max:20|unique:devices,internal_code,' . $id,
                'status' => 'required|in:active,inactive,canceled',
                'customer_id' => 'nullable|exists:customers,id',
                'cancellation_reason' => 'nullable|string',
                'cancelled_at' => 'nullable|date',
                'unlink_vehicle' => 'nullable|boolean',
                'unlink_reason' => 'nullable|string',
                'unlink_chip' => 'nullable|boolean',
                'new_gsm_card_id' => 'nullable|exists:gsm_cards,id',
                'new_vehicle_id' => 'nullable|exists:vehicles,id'
            ]);

            $device = Device::withTrashed()->findOrFail($id);

            // 💔 Lógica de Desvínculo de Veículo
            if ($request->boolean('unlink_vehicle')) {
                Log::notice("🚨 AUDITORIA: Hardware {$device->imei} desvinculado do veículo ID {$device->vehicle_id}. Motivo: " . ($request->unlink_reason ?? 'Não informado.'));
                $validated['vehicle_id'] = null;
            }

            // 💔 Lógica de Dissociação de Chip (SIM)
            if ($request->boolean('unlink_chip')) {
                Log::notice("🚨 AUDITORIA: Dissociação de Chip SIM para o Hardware {$device->imei}. Ativo enviado para manutenção.");
                // A dissociação acontece limpando o vínculo no Device (gsm_card_id)
                $validated['gsm_card_id'] = null;
            }

            // 📟 Lógica de Vínculo de Novo Veículo
            if ($request->filled('new_vehicle_id') && !$request->boolean('unlink_vehicle')) {
                $vehicleId = $request->new_vehicle_id;
                $validated['vehicle_id'] = $vehicleId;
                Log::notice("📟 AUDITORIA: Hardware {$device->imei} vinculado ao Veículo ID {$vehicleId}.");
            }

            // 📡 Lógica de Ativação/Vínculo de Novo Chip
            if ($request->filled('new_gsm_card_id') && !$request->boolean('unlink_chip')) {
                $gsmCardId = $request->new_gsm_card_id;
                $validated['gsm_card_id'] = $gsmCardId;

                // Sincroniza o Chip para o mesmo cliente do Hardware
                $deviceCustomerId = $request->customer_id ?? $device->customer_id;
                GsmCard::where('id', $gsmCardId)->update([
                    'customer_id' => $deviceCustomerId
                ]);
                Log::notice("📡 AUDITORIA: Novo Chip ID {$gsmCardId} vinculado ao Hardware {$device->imei}.");
            }

            // 🕰️ Auditoria de Cancelamento
            if ($request->status === 'canceled') {
                if (!$request->cancelled_at && !$device->cancelled_at) {
                    $validated['cancelled_at'] = now();
                } else {
                    $validated['cancelled_at'] = $request->cancelled_at ?: $device->cancelled_at ?: now();
                }
            } else {
                $validated['cancelled_at'] = null;
                $validated['cancellation_reason'] = null;
            }

            $device->update($validated);

            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }

            return redirect('/devices')->with('success', 'Dados atualizados.');

        } catch (\Exception $e) {
            Log::error("❌ ERRO NA ATUALIZAÇÃO DE DEVICE", ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'Erro interno: ' . $e->getMessage()], 500);
        }
    }

    /**
     * INATIVAÇÃO (SOFT DELETE)
     */
    public function destroy($id)
    {
        $device = Device::findOrFail($id);
        
        if ($device->vehicle_id) {
            return response()->json(['message' => 'Rastreador vinculado a veículo. Desinstale primeiro.'], 403);
        }

        $device->delete();
        return response()->json(['success' => true]);
    }

    /**
     * RESTAURAÇÃO TÁTICA
     */
    public function restore($id)
    {
        $device = Device::onlyTrashed()->findOrFail($id);
        $device->restore();
        return redirect('/devices')->with('success', 'Equipamento restaurado.');
    }

    /**
     * 🔍 VALIDAÇÃO TÁTICA DE IMEI (API INTERNA)
     */
    public function checkImei($imei)
    {
        $device = Device::withTrashed()
            ->with(['customer', 'deviceModel'])
            ->where('imei', $imei)
            ->first();

        if ($device) {
            return response()->json([
                'exists' => true,
                'internal_code' => $device->internal_code,
                'customer' => $device->customer->name ?? 'ESTOQUE',
                'model' => $device->deviceModel->name ?? 'N/A',
                'status' => $device->trashed() ? 'INATIVO (LIXEIRA)' : $device->status
            ]);
        }

        return response()->json(['exists' => false]);
    }
}
