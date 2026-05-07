<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditables;

class Attendance extends Model
{
    use HasFactory, SoftDeletes, Auditables;

    protected $fillable = [
        'customer_id',
        'vehicle_id',
        'user_id',
        'type',
        'log_path'
    ];

    /**
     * Relacionamento: Cliente atendido
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relacionamento: Veículo alvo da intervenção
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Relacionamento: Usuário/Atendente que realizou o atendimento
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Gera o caminho padrão para o arquivo de log físico (.txt)
     * Padrão RTECH: storage/app/atendimentos/{id_cliente}/atendimento_{id_veiculo}_{data}.txt
     */
    public static function generateLogPath($customerId, $vehicleId)
    {
        $date = now()->format('Y-m-d_H-i-s');
        return "atendimentos/{$customerId}/atendimento_{$vehicleId}_{$date}.txt";
    }
}
