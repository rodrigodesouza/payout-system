<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice',
        'user_id',
        'beneficiary_name',
        'code_bank',
        'number_agency',
        'number_account',
        'value',
        'status',
        'processor_bank_id'
    ];

    public function scopeByUser($query)
    {
        return $query->where('user_id', auth()->id());
    }
}
