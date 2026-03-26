<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\CustomerSubUser;
use App\Models\Customer;

class CustomerSubUserController extends Controller
{
    /**
     * Lista todos os Sub-Usuários vinculados a Clientes.
     */
    public function index()
    {
        // 🧬 Buscando acessos com o cliente vinculado
        $subUsers = CustomerSubUser::with('customer')->paginate(15);
        $customers = Customer::orderBy('name')->get();
        
        return view('customer-sub-users.index', compact('subUsers', 'customers'));
    }

    /**
     * Cadastra um novo acesso externo para um cliente.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'name' => 'required|max:100',
            'email' => 'required|email|unique:customer_sub_users,email',
            'external_username' => 'required|unique:customer_sub_users,external_username|max:50',
            'external_password' => 'required|min:6',
            'role' => 'nullable|max:50'
        ]);

        CustomerSubUser::create($validated);

        return redirect()->back()->with('success', '🔑 Acesso de Cliente criado com sucesso!');
    }

    /**
     * Inativa um acesso de cliente.
     */
    public function destroy($id)
    {
        $subUser = CustomerSubUser::findOrFail($id);
        $subUser->delete();

        return redirect()
            ->route('customer-sub-users.index')
            ->with('success', 'Acesso removido do portal do cliente.');
    }
}
