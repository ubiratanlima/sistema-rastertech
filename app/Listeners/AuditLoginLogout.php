<?php

namespace App\Listeners;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Request;

class AuditLoginLogout
{
    /**
     * Registra o evento de Login ou Logout na Auditoria.
     */
    public function handle($event)
    {
        $type = ($event instanceof Login) ? 'login' : 'logout';
        $user = $event->user;

        AuditLog::create([
            'user_id'        => $user->id,
            'event'          => $type,
            'auditable_type' => get_class($user),
            'auditable_id'   => $user->id,
            'old_values'     => null,
            'new_values'     => ['message' => "Usuário realizou {$type} no sistema."],
            'url'            => Request::fullUrl(),
            'ip_address'     => Request::header('X-Forwarded-For') ?? Request::ip(),
            'user_agent'     => Request::header('User-Agent'),
        ]);
    }
}
