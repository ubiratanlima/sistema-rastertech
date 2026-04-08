<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['plate', 'brand', 'model', 'customer_id'];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function devices() { return $this->hasMany(Device::class); }
    public function attendances() { return $this->hasMany(Attendance::class); }
}
