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
        $user = auth()->user();
        $userRole = strtolower($user->role);
        $isAdminLevel = in_array($userRole, ['admin', 'gerente', 'suporte', 'administrador']);

        $search = $request->input('search');
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');
        $view = $request->input('view', 'active');
        $selectedCustomerId = $request->input('customer_id');
        $selectedRole = $request->input('role');

        $query = CustomerSubUser::with(['customer', 'platform', 'driver']);

        // 🔄 AUTO-SYNC TÁTICO: Garante que Motoristas do PortalDriver apareçam aqui
        if (!$isAdminLevel && $user->customer_id) {
            $driversMissingSubUser = \App\Models\PortalDriver::where('customer_id', $user->customer_id)
                ->whereNotExists(function($q) use ($user) {
                    $q->select(\DB::raw(1))
                      ->from('customer_sub_users')
                      ->where('customer_id', $user->customer_id)
                      ->whereRaw('customer_sub_users.email = portal_drivers.email');
                })->get();

            foreach ($driversMissingSubUser as $driver) {
                $email = $driver->email ?? ($driver->cpf . '@rastertech.com.br');
                $platform = Platform::first();
                
                // Evita duplicidade se já existir um subuser com este email em outro cliente
                $exists = CustomerSubUser::where('email', $email)->exists();
                if ($exists) continue;

                $subUser = CustomerSubUser::create([
                    'customer_id' => $user->customer_id,
                    'platform_id' => $platform ? $platform->id : 1,
                    'name' => $driver->name,
                    'email' => $email,
                    'role' => 'Motorista',
                    'external_username' => $email,
                    'external_password' => '123456',
                    'access_validated' => true
                ]);

                $driver->update(['sub_user_id' => $subUser->id, 'email' => $email]);

                User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $subUser->name,
                        'email' => $subUser->email,
                        'password' => Hash::make($subUser->external_password),
                        'role' => 'Motorista',
                        'customer_id' => $subUser->customer_id,
                        'access_validated' => true,
                        'external_username' => $subUser->external_username,
                        'external_password' => $subUser->external_password
                    ]
                );
            }

            // 🗑️ LIMPEZA DE ÓRFÃOS: Remove credenciais que não existem na Ficha de Motorista
            $orphanedSubUsers = CustomerSubUser::where('customer_id', $user->customer_id)
                ->where('role', 'Motorista')
                ->whereNotExists(function($q) {
                    $q->select(\DB::raw(1))
                      ->from('portal_drivers')
                      ->whereRaw('portal_drivers.email = customer_sub_users.email');
                })->get();

            foreach ($orphanedSubUsers as $orphan) {
                // Remove o User vinculado também
                User::where('email', $orphan->email)->delete();
                $orphan->delete();
            }
        }

        // 🛡️ ISOLAMENTO DE DADOS (MULTI-TENANT)
        if (!$isAdminLevel) {
            // Se não for Admin, trava no cliente do usuário logado
            $query->where('customer_id', $user->customer_id);
            $selectedCustomerId = $user->customer_id;
            
            // 🛑 SEGURANÇA MÁXIMA: Autorizado NUNCA vê outros Autorizados
            // Ele só pode gerenciar Motoristas da sua frota.
            if (trim(strtolower($user->role)) === 'autorizado') {
                $query->where(\DB::raw('LOWER(role)'), 'motorista');
                $selectedRole = 'Motorista'; // Força o título/filtro visual
            }
        } elseif ($selectedCustomerId) {
            $query->where('customer_id', $selectedCustomerId);
        }

        // 🔍 FILTRO POR CARGO (Se não foi forçado acima)
        if ($selectedRole && trim(strtolower($user->role)) !== 'autorizado') {
            $query->where(\DB::raw('LOWER(role)'), trim(strtolower($selectedRole)));
        }

        if ($view === 'trash') {
            $query->onlyTrashed();
        }

        if ($search) {
            $searchLower = strtolower($search);
            $query->where(function($q) use ($searchLower) {
                $q->where(\DB::raw('LOWER(name)'), 'like', "%{$searchLower}%")
                  ->orWhere(\DB::raw('LOWER(email)'), 'like', "%{$searchLower}%")
                  ->orWhere(\DB::raw('LOWER(external_username)'), 'like', "%{$searchLower}%");
                
                // Se for admin, a busca também olha o nome do cliente
                $q->orWhereHas('customer', function($cq) use ($searchLower) {
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
        
        return view('customer-sub-users.index', compact(
            'subUsers', 'customers', 'platforms', 'search', 'sort', 
            'direction', 'view', 'selectedCustomerId', 'isAdminLevel', 'selectedRole', 'userRole'
        ));
    }

    /**
     * Cadastra um novo acesso externo para um cliente.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $isAutorizado = strtolower($user->role) === 'autorizado';

        $rules = [
            'customer_id' => 'required|exists:customers,id',
            'platform_id' => 'required|exists:platforms,id',
            'name' => 'required|max:100',
            'email' => 'required|email|unique:customer_sub_users,email',
            'role' => 'required|in:Autorizado',
            'external_password' => 'required|min:4',
        ];

        if ($request->role === 'Motorista') {
            return redirect()->back()->with('error', '🚛 Motoristas devem ser cadastrados pelo Portal de Clientes (Ficha de Motorista) para validação legal.');
        }

        $validated = $request->validate($rules);

        $validated['external_username'] = $validated['email'];
        $validated['validation_token'] = Str::random(60);
        $subUser = CustomerSubUser::create($validated);

        // 🔗 PONTE: Criar ou atualizar o registro na tabela de Users (Segurança / Login)
        User::updateOrCreate(
            ['external_username' => $subUser->external_username],
            [
                'name' => $subUser->name,
                'email' => $subUser->email,
                'password' => Hash::make($subUser->external_password),
                'role' => $subUser->role,
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
        $user = auth()->user();
        $isAutorizado = strtolower($user->role) === 'autorizado';

        $rules = [
            'customer_id' => 'required|exists:customers,id',
            'platform_id' => 'required|exists:platforms,id',
            'name' => 'required|max:100',
            'email' => 'required|email|unique:customer_sub_users,email,' . $id,
            'role' => 'required|in:Motorista,Autorizado',
            'external_password' => 'nullable|min:4',
        ];

        if ($isAutorizado) {
            $rules['role'] = 'required|in:Motorista';
        }

        $validated = $request->validate($rules);

        $validated['external_username'] = $validated['email'];

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
            'role' => $subUser->role,
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
            'access_validated' => true,
            'validation_method' => 'email'
        ]);

        // ✅ Sincronizar ativação na tabela de Users
        \App\Models\User::where('external_username', $subUser->external_username)->update([
            'email_verified_at' => now(),
            'validation_token' => null,
            'access_validated' => true,
            'validation_method' => 'email'
        ]);

        return view('customer-sub-users.verified', compact('subUser'))
            ->with('success', 'E-mail validado com sucesso! Sua conta está ativada.');
    }

    /**
     * Validação Manual pelo Administrador (Bypass de E-mail)
     */
    public function validateManual($id)
    {
        try {
            $subUser = CustomerSubUser::findOrFail($id);
            $validatorId = auth()->id();
            
            // 🛡️ Auditoria obrigatória: Quem, Quando e Como
            $subUser->update([
                'email_verified_at' => now(),
                'validation_token' => null,
                'access_validated' => true,
                'validation_method' => 'manual',
                'validated_by' => $validatorId
            ]);

            // ✅ Sincronizar ativação na tabela de Users (Login Central)
            \App\Models\User::where('external_username', $subUser->external_username)->update([
                'email_verified_at' => now(),
                'validation_token' => null,
                'access_validated' => true,
                'validation_method' => 'manual',
                'validated_by' => $validatorId
            ]);

            return redirect()->back()->with('success', '✅ Acesso de ' . $subUser->name . ' validado manualmente!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '❌ Erro no servidor: ' . $e->getMessage());
        }
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
