<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerSubUser extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'customer_id', 'platform_id', 'name', 'email', 'role', 
        'external_username', 'external_password',
        'email_verified_at', 'validation_token', 'access_validated'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'access_validated' => 'boolean'
    ];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function platform() { return $this->belongsTo(Platform::class); }
    public function isVerified() { return !is_null($this->email_verified_at); }
}
