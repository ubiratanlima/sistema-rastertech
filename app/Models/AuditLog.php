<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

use Illuminate\Database\Eloquent\Prunable;

class AuditLog extends Model
{
    use Prunable;

    /**
     * Define quais registros devem ser limpos automaticamente.
     * Registros com mais de 180 dias serão excluídos ao rodar 'php artisan model:prune'.
     */
    public function prunable()
    {
        return static::where('created_at', '<=', now()->subMonths(6));
    }

    public $timestamps = true;
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'event',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'url',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }
}
