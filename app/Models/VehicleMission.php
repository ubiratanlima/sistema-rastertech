<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleMission extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'vehicle_id',
        'driver_id',
        'entry_id',
        'exit_id',
        'status'
    ];

    /**
     * Relacionamento com o Checkout (Saída)
     */
    public function exitChecklist()
    {
        return $this->belongsTo(VehicleChecklist::class, 'exit_id');
    }

    /**
     * Relacionamento com o Check-in (Entrada)
     */
    public function entryChecklist()
    {
        return $this->belongsTo(VehicleChecklist::class, 'entry_id');
    }

    /**
     * Relacionamento com o Veículo
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Relacionamento com o Motorista que iniciou a jornada
     */
    public function driver()
    {
        return $this->belongsTo(PortalDriver::class, 'driver_id');
    }

    /**
     * Relacionamento com o Cliente (Multi-tenancy)
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
