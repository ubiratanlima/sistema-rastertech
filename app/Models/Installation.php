<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditables;

class Installation extends Model
{
    use HasFactory, SoftDeletes, Auditables;

    protected $fillable = [
        'installer_id',
        'customer_name',
        'vehicle_plate',
        'vehicle_details',
        'has_block',
        'status',
        'checkin_at',
        'processed_at',
        'completed_at',
        'checkin_photos',
        'process_photos',
        'checkout_photos',
        'checkout_notes',
        'customer_id_photo',
        'test_online',
        'test_block',
        'test_ignition_on',
        'test_ignition_off',
        'validator_id',
        'validated_at',
        'validation_notes',
        'validation_status'
    ];

    /**
     * 📸 CASTING DE EVIDÊNCIAS POR FASE
     */
    protected $casts = [
        'has_block' => 'boolean',
        'checkin_photos' => 'array',
        'process_photos' => 'array',
        'checkout_photos' => 'array',
        'checkin_at' => 'datetime',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'validated_at' => 'datetime',
        'test_online' => 'boolean',
        'test_block' => 'boolean',
        'test_ignition_on' => 'boolean',
        'test_ignition_off' => 'boolean'
    ];

    /**
     * 🛡️ PERFIL DO TÉCNICO (INSTALADOR)
     */
    public function installer()
    {
        return $this->belongsTo(User::class, 'installer_id');
    }

    /**
     * 🕵️ VALIDADOR (ATENDENTE/GERENTE)
     */
    public function validator()
    {
        return $this->belongsTo(User::class, 'validator_id');
    }
}
