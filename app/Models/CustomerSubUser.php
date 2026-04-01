<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerSubUser extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['customer_id', 'name', 'email', 'whatsapp', 'role', 'permissions', 'external_username', 'external_password', 'nickname'];

    public function customer() { return $this->belongsTo(Customer::class); }
}
