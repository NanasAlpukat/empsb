<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class history extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id',
        'bill_name',
        'major_name',
        'status',
        'history_time',
        'price',
    ];
    // public function bill(){
    //     return $this->belongsTo(bill::class);
    // }
    public function student(){
        return $this->belongsTo(student::class);
    }
    public function transaction(){
        return $this->belongsTo(transaction::class);
    }
    // public function major(){
    //     return $this->belongsTo(major::class);
    // }
}
