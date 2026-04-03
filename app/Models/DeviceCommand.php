<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeviceCommand extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['device_model_id', 'description', 'command_template', 'execution_order'];

    public function deviceModel() { return $this->belongsTo(DeviceModel::class); }
}
