<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditables;

class VehicleChecklist extends Model
{
    use HasFactory, Auditables;

    protected $fillable = [
        'customer_id',
        'vehicle_id',
        'driver_id',
        'performed_by_id',
        'type',
        'odometer',
        'fuel_level',
        'photos',
        'notes'
    ];

    protected $casts = [
        'photos' => 'json'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(PortalDriver::class, 'driver_id');
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by_id');
    }
}
