<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Lista todos os Administradores do Sistema.
     */
    public function index()
    {
        // 🧬 Listando quem comanda o ERP
        $users = User::paginate(10);
        
        return view('users.index', compact('users'));
    }

    /**
     * Cadastra um novo Administrador.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|max:50',
            'password' => 'required|min:8|confirmed'
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->back()->with('success', '🛡️ Novo Comandante registrado no quartel-general!');
    }

    /**
     * Inativa um acesso administrativo (não pode se auto-deletar).
     */
    public function destroy($id)
    {
        if (auth()->id() == $id) {
            return redirect()->back()->with('error', 'Auto-bloqueio negado! Você não pode remover o seu próprio acesso.');
        }

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'Acesso administrativo removido com sucesso.');
    }

    /**
     * Atualiza a preferência de tema (Claro/Escuro) do usuário.
     */
    public function updateTheme(Request $request)
    {
        $validated = $request->validate([
            'theme' => 'required|in:light,dark'
        ]);

        $user = auth()->user();
        $user->theme = $validated['theme'];
        $user->save();

        return response()->json(['success' => true]);
    }
}
