<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'document', 'code', 'is_default_stock'];

    public function vehicles() { return $this->hasMany(Vehicle::class); }
    public function devices() { return $this->hasMany(Device::class); }
    public function subUsers() { return $this->hasMany(CustomerSubUser::class); }
}
