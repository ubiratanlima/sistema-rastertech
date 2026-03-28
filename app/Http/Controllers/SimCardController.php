<?php

namespace App\Http\Controllers;

use App\Models\GsmCard;
use App\Models\Customer;
use App\Models\Device;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SimCardController extends Controller
{
    /**
     * GESTÃO DE CHIPS (MODO OPERAÇÃO)
     * Lista apenas os chips ativos no inventário.
     */
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'updated_at');
        $direction = $request->get('direction', 'desc');
        $search = $request->get('search');
        $view = $request->get('view', 'active'); // Opções: active, inventory, trash, all

        // 🏗️ ENGINE DE CONSULTA UNIVERSAL (Neutraliza SoftDeletes para controle total)
        $query = GsmCard::withTrashed()
            ->with(['customer', 'device.customer'])
            ->select('gsm_cards.*')
            ->leftJoin('devices', 'devices.gsm_card_id', '=', 'gsm_cards.id')
            ->leftJoin('customers', 'customers.id', '=', 'devices.customer_id');

        // 🔍 ENGINE DE BUSCA MULTI-DADOS (ICCID, Número ou Cliente)
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('customers.name', 'ILIKE', "%$search%")
                  ->orWhere('gsm_cards.iccid', 'ILIKE', "%$search%")
                  ->orWhere('gsm_cards.phone_number', 'ILIKE', "%$search%");
            });
        }

        // ⚙️ FILTRO DINÂMICO DE VISÃO (QUADRI-ESTADO)
        switch ($view) {
            case 'inventory':
                // ESTOQUE: Não deletado e status não-operacional (ex: 'inactive')
                $query->whereNull('gsm_cards.deleted_at')
                      ->whereNotIn('gsm_cards.status', ['active', 'canceled']);
                break;
            case 'canceled':
                // CANCELADO: Operação Master de bloqueio/desmame
                $query->whereNull('gsm_cards.deleted_at')
                      ->where('gsm_cards.status', 'canceled');
                break;
            case 'trash':
                // INATIVOS: Registros excluídos mas mantidos para auditoria (SoftDelete)
                $query->whereNotNull('gsm_cards.deleted_at');
                break;
            case 'active':
            default:
                // ATIVO: Não deletado e status 'active'
                $view = 'active'; // Garante o valor para a view
                $query->whereNull('gsm_cards.deleted_at')
                      ->where('gsm_cards.status', 'active');
                break;
        }

        // ⚖️ MOTOR DE ORDENAÇÃO AMBIGUIDADE-ZERO
        $sortColumns = [
            'id' => 'gsm_cards.id',
            'iccid' => 'gsm_cards.iccid',
            'numero' => 'gsm_cards.phone_number',
            'operator' => 'gsm_cards.operator',
            'status' => 'gsm_cards.status',
            'cliente' => 'customers.name',
            'equipamento' => 'devices.model_description'
        ];

        $orderBySelect = $sortColumns[$sort] ?? 'gsm_cards.updated_at';
        $query->orderBy($orderBySelect, $direction);

        $sims = $query->paginate(15)
                ->withPath('/sim-cards')
                ->withQueryString();

        // Abaixo, transformamos o objeto para manter a compatibilidade com a View antiga temporariamente
        $sims->getCollection()->transform(function($sim) {
            $sim->customer_name = $sim->customer->name ?? optional($sim->device)->customer->name ?? 'ESTOQUE';
            $sim->rtech_code = optional($sim->device)->model_description ?? '---';
            return $sim;
        });

        $customers = Customer::orderBy('name', 'asc')->get(['id', 'name']);
        $devices = Device::whereNull('gsm_card_id')->orderBy('model_description', 'asc')->get(['id', 'model_description']);
        $providers = Provider::where('type', 'connectivity')->orderBy('name', 'asc')->get(['id', 'name']);

        return view('sim-cards.index', compact('sims', 'sort', 'direction', 'search', 'view', 'customers', 'devices', 'providers'));
    }

    /**
     * LIXEIRA TÁTICA (MODO GESTOR)
     * Lista apenas chips deativados.
     */
    public function trash()
    {
        // 🔒 SEGURANÇA: Apenas administradores/gestores acessam a lixeira
        if (!auth()->check() || (auth()->user()->role !== 'admin' && auth()->user()->role !== 'manager')) {
            return redirect()->route('sim-cards.index')->with('error', 'Acesso negado à Lixeira Tática.');
        }

        $sims = GsmCard::onlyTrashed()
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withPath('/sim-cards/trash');
        return view('sim-cards.trash', compact('sims'));
    }

    /**
     * REGISTRO DE NOVO ATIVO NO INVENTÁRIO
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $isMassive = $request->has('chips') && is_array($request->get('chips'));
            
            if ($isMassive) {
                $chipsData = $request->get('chips');
                $count = 0;
                foreach ($chipsData as $data) {
                    $chip = GsmCard::create([
                        'iccid' => $data['iccid'],
                        'phone_number' => $data['phone_number'] ?? null,
                        'pin' => $data['pin'] ?? null,
                        'puk' => $data['puk'] ?? null,
                        'pin2' => $data['pin2'] ?? null,
                        'puk2' => $data['puk2'] ?? null,
                        'operator' => $request->get('operator'),
                        'provider_id' => $request->get('provider_id'),
                        'apn' => $request->get('apn'),
                        'apn_user' => $request->get('apn_user'),
                        'apn_pass' => $request->get('apn_pass'),
                        'status' => $data['status'] ?? 'inactive'
                    ]);
                    $count++;
                }
                DB::commit();
                return response()->json(['success' => true, 'message' => "📟 $count CHIPS REGISTRADOS EM MASSA COM SUCESSO!"]);
            } else {
                $validated = $request->validate([
                    'iccid' => 'required|unique:gsm_cards,iccid|max:50',
                    'phone_number' => 'nullable|max:20',
                    'pin' => 'nullable|max:10',
                    'puk' => 'nullable|max:20',
                    'pin2' => 'nullable|max:10',
                    'puk2' => 'nullable|max:20',
                    'operator' => 'required|max:50',
                    'provider_id' => 'nullable|exists:providers,id',
                    'apn' => 'nullable|max:100',
                    'apn_user' => 'nullable|max:50',
                    'apn_pass' => 'nullable|max:50',
                    'status' => 'required|in:active,inactive,canceled'
                ]);

                $chip = GsmCard::create($validated);
                DB::commit();
                
                return response()->json(['success' => true, 'message' => '📟 CHIP REGISTRADO COM SUCESSO!']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'FALHA NO REGISTRO!',
                    'error' => $e->getMessage()
                ], 422);
            }
            throw $e;
        }
    }

    /**
     * DESATIVAÇÃO SEGURA (SOFT DELETE)
     */
    public function destroy($id)
    {
        $chip = GsmCard::findOrFail($id);

        // 🔒 VERIFICAÇÃO: Chip vinculado a rastreador?
        if ($chip->device()->exists()) {
            $msg = 'Este chip está vinculado a um rastreador. Desvincule-o antes de desativar.';
            if (request()->ajax()) {
                return response()->json(['message' => $msg], 422);
            }
            return redirect()
                ->route('sim-cards.index')
                ->with('error', $msg);
        }

        $chip->delete(); // Soft Delete (Lixeira)

        if (request()->ajax()) {
            return response()->json(['message' => 'Chip movido para a lixeira tática com sucesso.']);
        }

        return redirect()
            ->route('sim-cards.index')
            ->with('warning', '⚠️ Chip movido para a Lixeira Tática.');
    }

    /**
     * RESTAURAÇÃO DE ATIVO
     */
    public function restore($id)
    {
        $chip = GsmCard::onlyTrashed()->findOrFail($id);
        $chip->restore();

        return redirect()
            ->route('sim-cards.trash')
            ->with('success', '📟 Chip restaurado e ativo no inventário.');
    }

    /**
     * EXCLUSÃO DEFINITIVA (MODO GESTOR)
     */
    public function forceDelete($id)
    {
        // 🔒 SEGURANÇA: Apenas administradores/gestores
        if (!auth()->check() || (auth()->user()->role !== 'admin' && auth()->user()->role !== 'manager')) {
             return redirect()->route('sim-cards.index')->with('error', 'Ação restrita a Administradores.');
        }

        $chip = GsmCard::onlyTrashed()->findOrFail($id);
        $chip->forceDelete();

        return redirect()
            ->route('sim-cards.trash')
            ->with('success', '💾 Registro do Chip eliminado definitivamente do banco.');
    }

    /**
     * ATUALIZAÇÃO TÁTICA DE CHIP
     */
    public function update(Request $request, $id)
    {
        try {
            \Log::info("📡 INÍCIO DE SINCRONIZAÇÃO TÁTICA (ID: $id)", [
                'payload' => $request->all(),
                'ajax' => $request->ajax()
            ]);

            $validated = $request->validate([
                'phone_number' => 'nullable|max:20',
                'operator' => 'required|max:50',
                'status' => 'required|in:active,inactive,canceled',
                'cancellation_reason' => 'nullable|string',
                'cancelled_at' => 'nullable|date',
                'customer_id' => 'nullable|exists:customers,id'
            ]);

            $chip = GsmCard::findOrFail($id);

            // 🕰️ LÓGICA DE AUDITORIA DE CANCELAMENTO
            if ($request->status === 'canceled') {
                // Prioritiza data retroativa se enviada, senão usa agora
                if (!$request->cancelled_at && !$chip->cancelled_at) {
                    $validated['cancelled_at'] = now();
                } else {
                    $validated['cancelled_at'] = $request->cancelled_at ?: $chip->cancelled_at ?: now();
                }
            } else {
                // Se está saindo do estado cancelado, limpa os rastros de cancelamento
                $validated['cancelled_at'] = null;
                $validated['cancellation_reason'] = null;
            }

            $chip->update($validated);

            \Log::info("💾 PERSISTÊNCIA CONCLUÍDA (ID: $id)");

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => '📟 Dados do Chip sincronizados com sucesso!'
                ]);
            }

            return redirect()
                ->route('sim-cards.index')
                ->with('success', '📟 Dados do Chip sincronizados com sucesso!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning("⚠️ FALHA DE VALIDAÇÃO (ID: $id)", ['errors' => $e->errors()]);
            if ($request->ajax()) {
                $errors = collect($e->errors())->flatten()->all();
                return response()->json(['message' => 'Validação falhou: ' . implode(', ', $errors)], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            \Log::error("❌ ERRO CRÍTICO NA ATUALIZAÇÃO (ID: $id)", ['error' => $e->getMessage()]);
            if ($request->ajax()) {
                return response()->json(['message' => 'Erro interno ao salvar dados.'], 500);
            }
            throw $e;
        }
    }
}
