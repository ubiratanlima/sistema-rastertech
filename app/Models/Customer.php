<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'company_name', 'email', 'document', 'code',
        'cell_phone', 'landline_phone', 'zip_code', 'street',
        'number', 'complement', 'neighborhood', 'city', 'notes'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function vehicles() { return $this->hasMany(Vehicle::class); }
    public function devices() { return $this->hasMany(Device::class); }
    public function gsmCards() { return $this->hasMany(GsmCard::class); }
    public function subUsers() { return $this->hasMany(CustomerSubUser::class); }
}
