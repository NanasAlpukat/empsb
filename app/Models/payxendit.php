<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class payxendit extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'order_id',
        'external_id',
        'xendit_id',
        'bank_code',
        'name',
        'email',
        'status',
        'currency',
        'account_number',
        'expiration_date',
        'expected_amount',
        'order_time',
    ];

    public function student(){
        return $this->belongsTo(student::class);
    }
    public function order(){
        return $this->belongsTo(order::class);
    }
}
