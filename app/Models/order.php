<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id',
        'bill_id',
        'major_id',
        'status',
        'expired_date',
        'price',
    ];
    public function bill(){
        return $this->belongsTo(bill::class);
    }
    public function student(){
        return $this->belongsTo(student::class);
    }
    public function transaction(){
        return $this->hasMany(transaction::class,'pivot_id');
    }
    public function mid(){
        return $this->hasMany(mid::class,'pivot_id');
    }
    public function payxendit(){
        return $this->hasMany(payxendit::class,'order_id');
    }
    public function major(){
        return $this->belongsTo(major::class);
    }
}
