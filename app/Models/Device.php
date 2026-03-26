<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Device extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'imei',
        'model_description',
        'device_model_id',
        'platform_id',
        'port_number',
        'gsm_card_id',
        'customer_id',
        'vehicle_id',
        'provider_id',
        'status'
    ];

    public function deviceModel(): BelongsTo { return $this->belongsTo(DeviceModel::class); }
    public function platform(): BelongsTo { return $this->belongsTo(Platform::class); }
    public function gsmCard(): BelongsTo { return $this->belongsTo(GsmCard::class); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function vehicle(): BelongsTo { return $this->belongsTo(Vehicle::class); }
    public function provider(): BelongsTo { return $this->belongsTo(Provider::class); }
}
