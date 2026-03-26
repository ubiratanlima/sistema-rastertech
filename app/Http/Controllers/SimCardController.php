<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SimCardController extends Controller
{
    /**
     * Lista todos os Cartões SIM (Chips) do sistema.
     */
    public function index()
    {
        // Buscando chips com os dados do dispositivo vinculado (se houver)
        $sims = DB::table('gsm_cards')
            ->leftJoin('devices', 'gsm_cards.id', '=', 'devices.gsm_card_id')
            ->leftJoin('customers', 'devices.customer_id', '=', 'customers.id')
            ->select(
                'gsm_cards.*',
                'devices.imei as imei_vincidulado',
                'customers.name as customer_name'
            )
            ->paginate(15)
            ->withPath('/sim-cards');

        return view('sim-cards.index', compact('sims'));
    }

    /**
     * Armazena um novo Cartão SIM no Banco de Dados.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'iccid' => 'required|unique:gsm_cards,iccid|max:50',
            'phone_number' => 'nullable|max:20',
            'operator' => 'required|max:50',
            'status' => 'required|in:active,inactive,suspended'
        ]);

        \Illuminate\Support\Facades\DB::table('gsm_cards')->insert([
            'iccid' => $validated['iccid'],
            'phone_number' => $validated['phone_number'],
            'operator' => $validated['operator'],
            'status' => $validated['status'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', '📟 Chip cadastrado com sucesso no inventário!');
    }
}
