<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PortalDriver extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'sub_user_id',
        'email',
        'whatsapp',
        'name',
        'father_name',
        'mother_name',
        'birth_date',
        'birth_place',
        'nationality',
        'cpf',
        'rg',
        'issuer',
        'uf',
        'cnh_number',
        'issue_date',
        'cnh_expiry',
        'category',
        'territory_validity',
        'cnh_front_path',
        'cnh_back_path',
        'status',
        'last_checklist_at'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'issue_date' => 'date',
        'cnh_expiry' => 'date',
        'last_checklist_at' => 'datetime'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function subUser()
    {
        return $this->belongsTo(CustomerSubUser::class, 'sub_user_id');
    }

    public function checklists()
    {
        return $this->hasMany(VehicleChecklist::class, 'driver_id');
    }
}
