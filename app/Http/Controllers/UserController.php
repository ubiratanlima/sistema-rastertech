<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Lista todos os Administradores com Busca Tática e Visão de Inativos.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $view = $request->input('view', 'active');
        
        $query = User::query()->whereNull('customer_id');

        // 🟢 SELETOR DE VISÃO (Padrão Ouro)
        if ($view === 'trash') {
            $query->onlyTrashed();
        }

        // 🔍 BUSCA TÁTICA (Foco em Identificação)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%")
                  ->orWhere('role', 'ILIKE', "%{$search}%");
            });
        }

        $users = $query->orderBy('name', 'asc')->paginate(15)->appends($request->all());
        
        return view('users.index', compact('users', 'search', 'view'));
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
            'gender' => 'required|in:Masculino,Feminino',
            'password' => 'required|min:8|confirmed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('users/images', 'public');
            $validated['image'] = $path;
        }

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Usuário cadastrado com sucesso!'
            ]);
        }

        return redirect()->back()->with('success', 'Usuário cadastrado com sucesso!');
    }

    /**
     * Retorna os detalhes de um administrador em JSON (Dossiê).
     */
    public function show($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        
        $avatarUrl = $user->image 
            ? asset('storage/' . $user->image) 
            : "https://ui-avatars.com/api/?name=" . urlencode($user->name) . "&background=" . ($user->trashed() ? '6c757d' : '007bff') . "&color=fff";

        return response()->json([
            'success' => true,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'gender' => $user->gender,
                'has_photo' => !empty($user->image),
                'status' => $user->trashed() ? 'INATIVO' : 'ATIVO',
                'created_at' => $user->created_at->format('d/m/Y H:i'),
                'avatar' => $avatarUrl
            ]
        ]);
    }

    /**
     * Reativa um acesso previamente removido (RESTORE).
     */
    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->back()->with('success', '✅ Acesso administrativo reativado com sucesso!');
    }

    /**
     * Atualiza os dados de um administrador (via AJAX).
     */
    public function update(Request $request, $id)
    {
        $user = User::withTrashed()->findOrFail($id);

        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|max:50',
            'gender' => 'required|in:Masculino,Feminino',
            'password' => 'nullable|min:8|confirmed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];

        $validated = $request->validate($rules);

        // 🔐 ATUALIZAÇÃO OPCIONAL DE SENHA
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        // 📸 ATUALIZAÇÃO OPCIONAL DE FOTO
        if ($request->hasFile('image')) {
            // Remove a antiga se existir
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
            $path = $request->file('image')->store('users/images', 'public');
            $validated['image'] = $path;
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Usuário atualizado com sucesso!'
        ]);
    }

    /**
     * Inativa um acesso administrativo (SoftDelete).
     */
    public function destroy($id)
    {
        if (auth()->id() == $id) {
            return response()->json([
                'success' => false, 
                'message' => 'Auto-bloqueio negado! Você não pode remover o seu próprio acesso.'
            ], 403);
        }

        $user = User::findOrFail($id);
        $user->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Acesso inativado com sucesso!']);
        }

        return redirect()->route('users.index')->with('success', 'Acesso administrativo removido com sucesso.');
    }

    /**
     * Atualiza a preferência de tema (Claro/Escuro).
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
