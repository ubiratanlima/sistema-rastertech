<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditables;

class Platform extends Model
{
    use HasFactory, SoftDeletes, Auditables;
    protected $fillable = ['name', 'url', 'server_ip', 'server_ip2', 'dns1', 'dns2', 'supplier_name', 'app_android_url', 'app_ios_url'];

    public function devices() { return $this->hasMany(Device::class); }
    public function credentials() { return $this->hasMany(CustomerSubUser::class, 'platform_id'); }
}
