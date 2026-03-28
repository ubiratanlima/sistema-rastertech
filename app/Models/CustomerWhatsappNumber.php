<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerWhatsappNumber extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'whatsapp_number',
        'contact_name',
        'label'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
