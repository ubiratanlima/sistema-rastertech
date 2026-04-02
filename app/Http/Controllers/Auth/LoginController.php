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

        // 🚀 GATILHO DE PRIMEIRO ACESSO (Conforme sua ordem)
        if ($user->access_validated === false) {
            $user->update(['access_validated' => true]);
        }

        return redirect()->intended('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/login');
    }
}
