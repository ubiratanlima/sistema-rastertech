<?php
// 🛡️ MODELO DE SEGURANÇA RASTERTECH - ESTABILIZADO
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'role', 'image', 'gender', 'theme', 'password', 
        'customer_id', 'external_username', 'external_password',
        'validation_token', 'access_validated'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'access_validated' => 'boolean'
    ];

    /**
     * NORMALIZAÇÃO DE PATENTE (Padrão Unificado)
     * Converte variações de texto em chaves operacionais únicas.
     */
    public function getNormalizedRoleAttribute()
    {
        $role = strtolower($this->role ?? '');
        if ($role === 'administrador') return 'admin';
        if ($role === 'gerente' || $role === 'gestor') return 'gerente';
        if ($role === 'suporte técnico' || $role === 'suporte') return 'suporte';
        return $role;
    }

    /**
     * LÓGICA DE CUSTÓDIA (RBAC CENTRAL)
     * Define quem tem autoridade sobre quem na hierarquia do quartel-general.
     */
    public function canManage(User $targetUser)
    {
        $myRole = $this->normalized_role;
        $targetRole = $targetUser->normalized_role;

        // Administradores MASTER e o próprio dono da conta têm passe livre total
        if ($myRole === 'admin' || $this->id === $targetUser->id) {
            return true;
        }

        // Gerentes comandam todos, exceto os Administradores MASTER
        if ($myRole === 'gerente') {
            return $targetRole !== 'admin';
        }

        // Suporte Técnico pode gerenciar apenas o nível Cliente
        if ($myRole === 'suporte') {
            return $targetRole === 'cliente';
        }

        return false;
    }
}
