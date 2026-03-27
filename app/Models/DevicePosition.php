<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevicePosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'latitude',
        'longitude',
        'speed',
        'ignition',
        'transmitted_at'
    ];

    /**
     * Relacionamento: A posição pertence a um Rastreador (Device).
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
