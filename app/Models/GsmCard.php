<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GsmCard extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'iccid',
        'phone_number',
        'operator',
        'status',
    ];

    /**
     * Relacionamento: O Chip pode estar associado a um Rastreador (Device).
     */
    public function device(): HasOne
    {
        return $this->hasOne(Device::class);
    }
}
