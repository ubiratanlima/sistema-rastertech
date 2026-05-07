<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use App\Traits\Auditables;

class Device extends Model
{
    use HasFactory, SoftDeletes, Auditables;
    
    /**
     * 🛰️ Engine de Identificação Universal Rastertech
     */
    protected static function booted()
    {
        static::creating(function ($device) {
            if (empty($device->internal_code)) {
                $lastId = DB::table('devices')->max('id') ?? 0;
                $device->internal_code = 'RTECH-' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    protected $fillable = [
        'imei',
        'internal_code',
        'model_description',
        'device_model_id',
        'platform_id',
        'port_number',
        'gsm_card_id',
        'customer_id',
        'vehicle_id',
        'provider_id',
        'status',
        'cancellation_reason',
        'cancelled_at'
    ];

    protected $casts = [
        'cancelled_at' => 'datetime'
    ];

    public function deviceModel(): BelongsTo { return $this->belongsTo(DeviceModel::class); }
    public function platform(): BelongsTo { return $this->belongsTo(Platform::class); }
    public function gsmCard(): BelongsTo { return $this->belongsTo(GsmCard::class); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function vehicle(): BelongsTo { return $this->belongsTo(Vehicle::class); }
    public function provider(): BelongsTo { return $this->belongsTo(Provider::class); }
}
