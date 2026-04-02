<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Models\CustomerSubUser;
use App\Models\Customer;
use App\Models\Platform;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Mail\SubUserVerification;

class CustomerSubUserController extends Controller
{
    /**
     * Lista todos os Sub-Usuários vinculados a Clientes.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');
        $view = $request->input('view', 'active');

        $query = CustomerSubUser::with(['customer', 'platform']);

        if ($view === 'trash') {
            $query->onlyTrashed();
        }

        if ($search) {
            $searchLower = strtolower($search);
            $query->where(function($q) use ($searchLower) {
                $q->where(\DB::raw('LOWER(name)'), 'like', "%{$searchLower}%")
                  ->orWhere(\DB::raw('LOWER(email)'), 'like', "%{$searchLower}%")
                  ->orWhere(\DB::raw('LOWER(external_username)'), 'like', "%{$searchLower}%")
                  ->orWhereHas('customer', function($cq) use ($searchLower) {
                      $cq->where(\DB::raw('LOWER(name)'), 'like', "%{$searchLower}%");
                  });
            });
        }

        $subUsers = $query->orderBy($sort, $direction)
            ->paginate(15)
            ->withPath('/customer-sub-users')
            ->withQueryString();

        $customers = Customer::orderBy('name')->get(['id', 'name', 'code']);
        $platforms = Platform::orderBy('name')->get(['id', 'name', 'url', 'app_android_url', 'app_ios_url']);
        
        return view('customer-sub-users.index', compact('subUsers', 'customers', 'platforms', 'search', 'sort', 'direction', 'view'));
    }

    /**
     * Cadastra um novo acesso externo para um cliente.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'platform_id' => 'required|exists:platforms,id',
            'name' => 'required|max:100',
            'email' => 'required|email|unique:customer_sub_users,email',
            'external_username' => 'required|unique:customer_sub_users,external_username|max:50',
            'external_password' => 'required|min:4',
        ]);

        $validated['validation_token'] = Str::random(60);
        $subUser = CustomerSubUser::create($validated);

        // 🔗 PONTE: Criar ou atualizar o registro na tabela de Users (Segurança / Login)
        User::updateOrCreate(
            ['external_username' => $subUser->external_username],
            [
                'name' => $subUser->name,
                'email' => $subUser->email,
                'password' => Hash::make($subUser->external_password),
                'role' => strtolower($subUser->role ?: 'operator'),
                'customer_id' => $subUser->customer_id,
                'validation_token' => $subUser->validation_token,
                'access_validated' => false,
                'external_username' => $subUser->external_username,
                'external_password' => $subUser->external_password
            ]
        );

        // 📧 DISPARO DE VALIDAÇÃO REAL
        Mail::to($subUser->email)->send(new SubUserVerification($subUser));

        return redirect()->back()->with('success', '🔑 Acesso criado! Um e-mail de validação foi enviado.');
    }

    /**
     * Atualiza dados de um credencial.
     */
    public function update(Request $request, $id)
    {
        $subUser = CustomerSubUser::findOrFail($id);
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'platform_id' => 'required|exists:platforms,id',
            'name' => 'required|max:100',
            'email' => 'required|email|unique:customer_sub_users,email,' . $id,
            'external_username' => 'required|unique:customer_sub_users,external_username,' . $id . '|max:50',
            'external_password' => 'nullable|min:4',
        ]);

        // 🔄 DETECÇÃO DE MUDANÇA DE E-MAIL
        if ($validated['email'] !== $subUser->email) {
            $validated['email_verified_at'] = null;
            $validated['validation_token'] = Str::random(60);
            $validated['access_validated'] = false;
        }

        if (empty($validated['external_password'])) {
            unset($validated['external_password']);
        }

        $subUser->update($validated);

        // 🔗 PONTE: Sincronizar mudança de e-mail/senha com a tabela Users
        $userData = [
            'name' => $subUser->name,
            'email' => $subUser->email,
            'role' => strtolower($subUser->role ?: 'operator'),
            'external_username' => $subUser->external_username,
            'external_password' => $subUser->external_password
        ];

        if (!empty($validated['external_password'])) {
            $userData['password'] = Hash::make($subUser->external_password);
        }

        if (!empty($validated['validation_token'])) {
            $userData['validation_token'] = $subUser->validation_token;
            $userData['email_verified_at'] = null; // Resetar se mudou o e-mail
        }

        User::updateOrCreate(['external_username' => $subUser->external_username], $userData);

        // Se o e-mail mudou, reenviar a validação real
        if (!empty($validated['validation_token'])) {
            Mail::to($subUser->email)->send(new SubUserVerification($subUser));
        }

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Acesso atualizado! Se o e-mail foi alterado, requer nova validação.');
    }

    /**
     * Valida o e-mail através do link.
     */
    public function verifyEmail($token)
    {
        $subUser = CustomerSubUser::where('validation_token', $token)->firstOrFail();
        
        $subUser->update([
            'email_verified_at' => now(),
            'validation_token' => null,
        ]);

        // ✅ Sincronizar ativação na tabela de Users
        \App\Models\User::where('external_username', $subUser->external_username)->update([
            'email_verified_at' => now(),
            'validation_token' => null
        ]);

        return view('customer-sub-users.verified', compact('subUser'))
            ->with('success', 'E-mail validado com sucesso! Sua conta está ativada.');
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

    /**
     * Reativa um acesso inativo.
     */
    public function restore($id)
    {
        $subUser = CustomerSubUser::onlyTrashed()->findOrFail($id);
        $subUser->restore();

        return redirect()->route('customer-sub-users.index')->with('success', 'Acesso reativado com sucesso!');
    }
}
