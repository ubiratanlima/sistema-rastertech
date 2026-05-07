<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditables;

class DeviceModel extends Model
{
    use HasFactory, SoftDeletes, Auditables;
    protected $fillable = ['name', 'manufacturer'];

    public function commands() { return $this->hasMany(DeviceCommand::class); }
    public function devices() { return $this->hasMany(Device::class); }
}
