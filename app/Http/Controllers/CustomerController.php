<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Customer;

class CustomerController extends Controller
{
    /**
     * Lista todos os Clientes com resumo de frota e código.
     */
    public function index()
    {
        $customers = Customer::withCount(['devices', 'vehicles', 'subUsers'])
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withPath('/customers');

        return view('customers.index', compact('customers'));
    }

    /**
     * Inativação segura (Soft Delete) com validação de frota.
     */
    public function destroy(Customer $customer)
    {
        // REGRA DE OURO RASTERTECH: Proibido inativar se houver equipamentos
        if ($customer->devices()->count() > 0) {
            return redirect()->back()->with('error', "BLOQUEIO DE SEGURANÇA: O cliente ainda possui {$customer->devices()->count()} unidades rastreadas vinculadas. Desvincule todos para inativar.");
        }

        $customer->delete(); // Soft Delete Laravel

        return redirect()->route('customers.index')->with('success', "Cliente inativado com sucesso. Custódia de dados preservada.");
    }
}
