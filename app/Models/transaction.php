<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'transaction_id',
        'pivot_id',
        'student_id',
        'name',
        'email',
        'order_id',
        'transaction_status',
        'transaction_time',
        'gross_amount',
        'fraud_status',
        'payment_type',
        'no_va',
        'bank',
    ];
    public function student(){
        return $this->belongsTo(student::class);
    }
    public function order(){
        return $this->belongsTo(order::class);
    }
    // public function history(){
    //     return $this->hasOne(history::class,'transaction_id');
    // }

}
