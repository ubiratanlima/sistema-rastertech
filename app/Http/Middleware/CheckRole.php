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
            // DEBUG TEMPORARIO: Exibir os papéis no abort para depuração rápida
            $debugRole = json_encode($userRoles);
            $debugRequired = json_encode($flatRoles);
            abort(403, "Acesso restrito. Sua Role no Banco: $debugRole | Exigido na Rota: $debugRequired");
        }

        return $next($request);
    }
}
