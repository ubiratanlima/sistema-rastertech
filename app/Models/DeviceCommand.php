<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceCommand extends Model
{
    use HasFactory;
    protected $fillable = ['device_model_id', 'description', 'command_template', 'execution_order'];

    public function deviceModel() { return $this->belongsTo(DeviceModel::class); }
}
