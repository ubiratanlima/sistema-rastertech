<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditables;

class Customer extends Model
{
    use HasFactory, SoftDeletes, Auditables;

    protected $fillable = [
        'asaas_id', 'origin', 'asaas_group', 'name', 'company_name', 'email', 'document', 'code',
        'cell_phone', 'landline_phone', 'zip_code', 'street',
        'number', 'complement', 'neighborhood', 'city', 'state', 'notes'
    ];

    protected $casts = [
        'email' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // 🕊️ PONTE ESTRUTURAL: Conversão String-JSON (Suporte Multi-Email)
    public function setEmailAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['email'] = json_encode($value);
        } else {
            $data = array_filter(array_map('trim', explode(',', (string)$value)));
            $this->attributes['email'] = json_encode(array_values($data));
        }
    }

    public function getEmailAttribute($value)
    {
        $data = json_decode($value, true);
        return is_array($data) ? implode(',', $data) : $value;
    }

    public function vehicles() { return $this->hasMany(Vehicle::class); }
    public function devices() { return $this->hasMany(Device::class); }
    public function gsmCards() { return $this->hasMany(GsmCard::class); }
    public function subUsers() { return $this->hasMany(CustomerSubUser::class); }
    public function drivers() { return $this->hasMany(PortalDriver::class); }
    public function attendances() { return $this->hasMany(Attendance::class); }
    public function users() { return $this->hasMany(User::class); }
}
