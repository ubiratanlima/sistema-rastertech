<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Platform extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'url', 'server_ip', 'supplier_name', 'app_android_url', 'app_ios_url'];

    public function devices() { return $this->hasMany(Device::class); }
    public function credentials() { return $this->hasMany(CustomerSubUser::class, 'platform_id'); }
}
