<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        // 🔍 TENTATIVA 1: POR E-MAIL (Administradores/Clientes)
        if (Auth::attempt(['email' => $credentials['login'], 'password' => $credentials['password']])) {
            return $this->handleSuccessLogin();
        }

        // 🔍 TENTATIVA 2: POR EXTERNAL USERNAME (Operadores/Motoristas)
        if (Auth::attempt(['external_username' => $credentials['login'], 'password' => $credentials['password']])) {
            return $this->handleSuccessLogin();
        }

        return back()->withErrors(['login' => 'As credenciais não conferem com nossos registros.']);
    }

    private function handleSuccessLogin()
    {
        $user = Auth::user();
        $role = strtolower($user->role ?? '');

        // 🚀 GATILHO DE PRIMEIRO ACESSO
        if ($user->access_validated === false) {
            $user->update(['access_validated' => true]);
        }

        // 🏎️ REDIRECIONAMENTO POR ROLE RTECH (Estrito)
        if ($role === 'motorista') {
            return redirect()->route('portal.verificacoes.index');
        }

        if ($role === 'instalador') {
            return redirect()->route('portal.instalador.index');
        }

        if (in_array($role, ['cliente', 'autorizado'])) {
            return redirect()->route('portal.dashboard');
        }

        // 🔍 BACKUP PARA SUB-USUÁRIOS LEGADOS
        $subUser = \App\Models\CustomerSubUser::where('external_username', $user->external_username)->first();
        if ($subUser) {
            $isDriver = \App\Models\PortalDriver::where('sub_user_id', $subUser->id)->exists();
            if ($isDriver) {
                return redirect()->route('portal.verificacoes.index');
            }
        }

        // 🛰️ ADMINISTRATIVOS (Admin, Gerente, Operador)
        return redirect()->to('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/login');
    }
}
