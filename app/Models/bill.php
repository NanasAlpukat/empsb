<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bill extends Model
{
    use HasFactory;
    protected $fillable = [
        'bill_name',
      
    ];
    public function order(){
        return $this->hasMany(order::class,'bill_id');
    }
    // public function history(){
    //     return $this->hasMany(history::class,'bill_id');
    // }
}
