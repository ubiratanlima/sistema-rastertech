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
        'validation_token', 'access_validated', 'validated_by', 'validation_method'
    ];

    public function validator() { return $this->belongsTo(User::class, 'validated_by'); }

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'access_validated' => 'boolean'
    ];

    /**
     * LÓGICA DE CUSTÓDIA (RBAC CENTRAL)
     * Define quem tem autoridade sobre quem na hierarquia do quartel-general.
     */
    public function canManage(User $targetUser)
    {
        $myRole = $this->role;
        $targetRole = $targetUser->role;

        // Administradores MASTER e o próprio dono da conta têm passe livre total
        if ($myRole === 'Administrador' || $this->id === $targetUser->id) {
            return true;
        }

        // Gerentes comandam apenas os níveis operacionais
        if ($myRole === 'Gerente') {
            return !in_array($targetRole, ['Administrador', 'Gerente']);
        }

        // Suporte pode gerenciar apenas o nível Cliente
        if ($myRole === 'Suporte') {
            return $targetRole === 'Cliente';
        }

        return false;
    }

    public function customer() { return $this->belongsTo(Customer::class); }
}
