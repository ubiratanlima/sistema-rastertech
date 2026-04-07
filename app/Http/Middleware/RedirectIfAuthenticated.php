<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();
                $subUser = \App\Models\CustomerSubUser::where('external_username', $user->external_username)->first();
                if ($subUser && \App\Models\PortalDriver::where('sub_user_id', $subUser->id)->exists()) {
                    return redirect()->route('portal.verificacoes.index');
                }

                // 🛰️ ESTABILIZAÇÃO OURO: Força o redirecionamento administrativo (sem porta :8000)
                return redirect(config('app.url'));
            }
        }

        return $next($request);
    }
}
