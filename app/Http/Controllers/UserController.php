<?php

namespace App\Http\Controllers;

/**
 * 🛡️ CONTROLADOR DE ACESSO TÁTICO
 * Gerencia a hierarquia de usuários e permissões do sistema.
 * Revisado para RBAC Centralizado no Model User em 24/04/2026.
 */

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
        $currentUser = auth()->user();
        
        $query = User::query();

        // 🛡️ REGRAS DE HIERARQUIA E VISIBILIDADE RBAC
        if ($currentUser->role === 'Gerente') {
            $query->where('role', '!=', 'Administrador');
        } elseif ($currentUser->role === 'Suporte Técnico') {
            $query->where(function($q) use ($currentUser) {
                $q->where('id', $currentUser->id)
                  ->orWhere('role', 'Cliente');
            });
        } elseif ($currentUser->role === 'Técnico Instalador') {
            $query->where('id', $currentUser->id);
        }

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
        $currentUser = auth()->user();
        $requestedRole = $request->input('role');
        
        // Blindagem RBAC: Validar se permite criação desse cargo
        if ($currentUser->role === 'Gerente' && $requestedRole === 'Administrador') {
            return response()->json(['success' => false, 'message' => 'Hierarquia insuficiente para criar Administradores.'], 403);
        }
        if ($currentUser->role === 'Suporte Técnico' && $requestedRole !== 'Cliente') {
            return response()->json(['success' => false, 'message' => 'Operadores só podem criar usuários do tipo Cliente.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:Técnico Instalador,Suporte Técnico,Administrador,Gerente,Cliente',
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
        
        if (!auth()->user()->canManage($user)) {
             return response()->json(['success' => false, 'message' => 'Acesso Negado: Perfil Hierárquico Insuficiente'], 403);
        }
        
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
        
        if (!auth()->user()->canManage($user)) {
            abort(403, 'Acesso Negado: Perfil Hierárquico Insuficiente');
        }

        $user->restore();

        return redirect()->back()->with('success', '✅ Acesso administrativo reativado com sucesso!');
    }

    /**
     * Atualiza os dados de um administrador (via AJAX).
     */
    public function update(Request $request, $id)
    {
        $userTarget = User::withTrashed()->findOrFail($id);
        $currentUser = auth()->user();

        if (!$currentUser->canManage($userTarget)) {
            return response()->json(['success' => false, 'message' => 'Acesso Negado: Perfil Hierárquico Insuficiente'], 403);
        }

        $requestedRole = $request->input('role');
        
        // Bloqueio de auto-elevação ou manipulação ilegal
        if ($currentUser->id === $userTarget->id && $requestedRole !== $currentUser->role && $currentUser->normalized_role !== 'admin') {
             return response()->json(['success' => false, 'message' => 'Você não tem permissão para alterar sua própria patente.'], 403);
        }
        
        if ($currentUser->normalized_role === 'gerente' && strtolower($requestedRole) === 'administrador' && $userTarget->normalized_role !== 'admin') {
             return response()->json(['success' => false, 'message' => 'Gerentes não podem promover acessos a Administrador MASTER.'], 403);
        }

        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:Técnico Instalador,Suporte Técnico,Administrador,Gerente,Cliente',
            'gender' => 'required|in:Masculino,Feminino',
            'password' => 'nullable|min:8|confirmed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];

        $validated = $request->validate($rules);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        if ($request->hasFile('image')) {
            if ($userTarget->image) {
                Storage::disk('public')->delete($userTarget->image);
            }
            $path = $request->file('image')->store('users/images', 'public');
            $validated['image'] = $path;
        }

        $userTarget->update($validated);

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
            return response()->json(['success' => false, 'message' => 'Auto-bloqueio negado! Você não pode remover o seu próprio acesso.'], 403);
        }

        $user = User::findOrFail($id);
        
        if (!auth()->user()->canManage($user)) {
             return response()->json(['success' => false, 'message' => 'Acesso Negado: Perfil Hierárquico Insuficiente'], 403);
        }

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
