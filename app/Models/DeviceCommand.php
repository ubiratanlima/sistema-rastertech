<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditables;

class DeviceCommand extends Model
{
    use HasFactory, SoftDeletes, Auditables;
    protected $fillable = ['device_model_id', 'description', 'command_template', 'execution_order'];

    public function deviceModel() { return $this->belongsTo(DeviceModel::class); }
}
