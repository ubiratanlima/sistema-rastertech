<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleExpense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'driver_id',
        'vehicle_id',
        'type',
        'description',
        'odometer',
        'amount',
        'fuel_liters',
        'receipt_photo',
        'date'
    ];

    protected $casts = [
        'date' => 'datetime',
        'amount' => 'decimal:2',
        'odometer' => 'decimal:2',
        'fuel_liters' => 'decimal:2',
    ];

    // 🔗 RELACIONAMENTOS TÁTICOS
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function driver()
    {
        return $this->belongsTo(PortalDriver::class, 'driver_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
