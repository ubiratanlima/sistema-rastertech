<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GsmCard extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'iccid',
        'phone_number',
        'operator',
        'apn',
        'apn_user',
        'apn_pass',
        'pin',
        'puk',
        'pin2',
        'puk2',
        'status',
        'cancellation_reason',
        'cancelled_at',
        'provider_id',
        'customer_id',
        'device_id'
    ];

    /**
     * Relacionamento: O Chip pode estar associado a um Rastreador (Device).
     */
    public function device(): HasOne
    {
        return $this->hasOne(Device::class);
    }

    /**
     * Relacionamento: O Chip pode pertencer diretamente a um Cliente.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
