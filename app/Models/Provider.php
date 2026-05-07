<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditables;

class Provider extends Model
{
    use HasFactory, SoftDeletes, Auditables;
    protected $fillable = ['name', 'type', 'email', 'phone', 'document', 'contact_name'];

    public function devices() { return $this->hasMany(Device::class); }
    public function gsmCards() { return $this->hasMany(GsmCard::class); }
}
