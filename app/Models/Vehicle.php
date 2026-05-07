<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditables;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes, Auditables;
    protected $fillable = [
        'plate', 'brand', 'model', 'year', 'color', 'renavam', 'chassi', 
        'photo_front', 'photo_back', 'customer_id'
    ];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function devices() { return $this->hasMany(Device::class); }
    public function attendances() { return $this->hasMany(Attendance::class); }
}
