<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $userRoles = [];
        
        // Determina o papel do usuário (Normalizado para minúsculo)
        $role = strtolower(Auth::user()->role ?? '');
        if ($role) {
            $userRoles[] = $role;
            
            // Aliases explícitos permitidos para retrocompatibilidade de nomes de exibição
            if ($role === 'gestor') $userRoles[] = 'gerente';
            if ($role === 'administrador') $userRoles[] = 'admin';

            // 🛡️ BLOQUEIO DE SEGURANÇA (Raiz): Motoristas com CNH vencida não operam
            if (in_array($role, ['motorista', 'driver'])) {
                $user = Auth::user();
                $subUser = \App\Models\CustomerSubUser::where('external_username', $user->external_username)->first();
                if ($subUser) {
                    $driverProfile = \App\Models\PortalDriver::where('sub_user_id', $subUser->id)->first();
                    if ($driverProfile && !$driverProfile->isValidCnh()) {
                        Auth::logout();
                        return redirect('/login')->withErrors(['login' => 'ACESSO INTERROMPIDO: Sua CNH está vencida. Procure o administrativo.']);
                    }
                }
            }
        }

        $allowed = false;
        $flatRoles = [];
        
        foreach ($roles as $r) {
            foreach (explode(',', $r) as $subRole) {
                $flatRoles[] = strtolower(trim($subRole));
            }
        }

        foreach ($flatRoles as $r) {
            if (in_array($r, $userRoles)) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed) {
            abort(403, "Acesso restrito. Sua conta não possui permissão para acessar esta funcionalidade.");
        }

        return $next($request);
    }
}
